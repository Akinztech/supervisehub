<?php
require_role('supervisor');
$projectId = (int)($_GET['id'] ?? 0);
$project = db_one(
    "SELECT p.*, u.full_name student_name, u.email student_email, u.matric_no
     FROM projects p
     JOIN users u ON u.id=p.student_id
     WHERE p.id=? AND p.supervisor_id=?",
    [$projectId, current_user()['id']]
);
if (!$project) { http_response_code(404); exit('Assigned project not found.'); }
$pageTitle = 'Student Project Portfolio';
$pageSubtitle = $project['student_name'] . ' · ' . ($project['matric_no'] ?? 'No matric number');
$progress = progress_for_project($projectId);
$milestones = db_all('SELECT * FROM milestones WHERE project_id=? ORDER BY id', [$projectId]);
$sessions = db_all('SELECT * FROM sessions WHERE project_id=? ORDER BY scheduled_at DESC', [$projectId]);
$documents = db_all(
    "SELECT d.*, u.full_name uploader_name
     FROM documents d JOIN users u ON u.id=d.uploaded_by
     WHERE d.project_id=? ORDER BY d.version_no DESC, d.id DESC",
    [$projectId]
);
include __DIR__.'/../includes/layout_top.php';
?>
<div class="project-hero card">
  <div>
    <span class="eyebrow">ASSIGNED STUDENT PROJECT</span>
    <h2><?= e($project['title']) ?></h2>
    <p><?= e($project['student_name']) ?> · <?= e($project['matric_no']) ?> · <?= e($project['student_email']) ?></p>
    <div class="tag-row"><span class="badge success"><?= e($project['status']) ?></span><span class="badge neutral"><?= e($project['current_stage']) ?></span></div>
  </div>
  <div class="progress-panel"><strong><?= $progress ?>%</strong><span>Approved progress</span><div class="progress-track"><div class="progress-bar" data-progress="<?= $progress ?>"></div></div></div>
</div>
<div class="stats-grid">
  <div class="stat-card"><div class="stat-label">Milestones</div><div class="stat-value"><?= count($milestones) ?></div><div class="stat-note"><?= count(array_filter($milestones, fn($m)=>$m['status']==='approved')) ?> approved</div></div>
  <div class="stat-card"><div class="stat-label">Sessions</div><div class="stat-value"><?= count($sessions) ?></div><div class="stat-note"><?= count(array_filter($sessions, fn($s)=>$s['status']==='completed')) ?> completed</div></div>
  <div class="stat-card"><div class="stat-label">Documents</div><div class="stat-value"><?= count($documents) ?></div><div class="stat-note">Version-controlled uploads</div></div>
  <div class="stat-card"><div class="stat-label">Due date</div><div class="stat-value compact"><?= e(format_date($project['due_date'],'d M Y')) ?></div><div class="stat-note">Expected completion</div></div>
</div>
<div class="card"><div class="card-header"><div><h2>Milestone plan</h2><p>Review status and feedback across the full project journey.</p></div><a class="button small" href="<?= e(url('index.php?page=supervisor-milestones')) ?>">Open review queue</a></div>
<div class="table-wrap"><table><thead><tr><th>Milestone</th><th>Weight</th><th>Due</th><th>Status</th><th>Student note</th><th>Supervisor comment</th></tr></thead><tbody>
<?php foreach($milestones as $m): ?><tr><td><strong><?= e($m['title']) ?></strong><br><span class="muted"><?= e($m['description']) ?></span></td><td><?= (int)$m['weight'] ?>%</td><td><?= e(format_date($m['due_date'],'d M Y')) ?></td><td><span class="badge <?= status_class($m['status']) ?>"><?= e(str_replace('_',' ',$m['status'])) ?></span></td><td><?= e($m['student_note'] ?: '—') ?></td><td><?= e($m['supervisor_comment'] ?: '—') ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<div class="dashboard-grid"><section class="card"><div class="card-header"><div><h2>Session history</h2><p>Scheduling, feedback, decisions and action records.</p></div><a class="button small secondary" href="<?= e(url('index.php?page=supervisor-sessions')) ?>">Manage sessions</a></div>
<?php if(!$sessions): ?><div class="empty-state">No sessions recorded.</div><?php else: foreach($sessions as $s): ?><div class="list-card"><div><h4><?= e(format_date($s['scheduled_at'])) ?> · <?= e(ucfirst($s['mode'])) ?></h4><p><strong>Agenda:</strong> <?= e($s['agenda']) ?></p><?php if($s['supervisor_feedback']): ?><p><strong>Feedback:</strong> <?= e($s['supervisor_feedback']) ?></p><?php endif; ?><?php if($s['action_items']): ?><p><strong>Actions:</strong> <?= e($s['action_items']) ?></p><?php endif; ?></div><span class="badge <?= status_class($s['status']) ?>"><?= e($s['status']) ?></span></div><?php endforeach; endif; ?></section>
<aside class="card"><div class="card-header"><div><h2>Documents</h2><p>Latest versions first</p></div></div><?php if(!$documents): ?><div class="empty-state">No documents uploaded.</div><?php else: foreach($documents as $d): ?><div class="document-item"><div style="display:flex;align-items:center;gap:12px"><div class="file-icon">V<?= (int)$d['version_no'] ?></div><div><strong><?= e($d['original_name']) ?></strong><div class="muted"><?= e($d['description'] ?: 'No description') ?><br><?= e(format_date($d['created_at'])) ?> · <?= number_format($d['file_size']/1024,1) ?> KB</div></div></div><a class="button small secondary" href="<?= e(url('download.php?id='.(int)$d['id'])) ?>" target="_blank" rel="noopener">Open</a></div><?php endforeach; endif; ?></aside></div>
<?php include __DIR__.'/../includes/layout_bottom.php'; ?>
