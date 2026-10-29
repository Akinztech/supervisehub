<?php
require_login();
$pageTitle = 'Project Messages';
$pageSubtitle = 'Direct communication linked to the project record';
$u = current_user();
$projects = [];
$project = null;
$partner = null;

if ($u['role'] === 'student') {
    $project = project_for_student((int)$u['id']);
    if ($project && $project['supervisor_id']) {
        $partner = db_one('SELECT id,full_name,email FROM users WHERE id=? AND role=\'supervisor\'', [$project['supervisor_id']]);
    }
} elseif ($u['role'] === 'supervisor') {
    $projects = db_all(
        "SELECT p.*, s.full_name student_name, s.email student_email, s.matric_no
         FROM projects p JOIN users s ON s.id=p.student_id
         WHERE p.supervisor_id=? ORDER BY s.full_name",
        [$u['id']]
    );
    $requestedProjectId = (int)($_GET['project_id'] ?? 0);
    foreach ($projects as $candidate) {
        if (($requestedProjectId && (int)$candidate['id'] === $requestedProjectId) || (!$requestedProjectId && !$project)) {
            $project = $candidate;
        }
    }
    if ($project) {
        $partner = db_one('SELECT id,full_name,email FROM users WHERE id=? AND role=\'student\'', [$project['student_id']]);
    }
}

$msgs = $project ? db_all(
    "SELECT m.*, s.full_name sender_name
     FROM messages m JOIN users s ON s.id=m.sender_id
     WHERE m.project_id=? AND (m.sender_id=? OR m.receiver_id=?)
     ORDER BY m.created_at",
    [$project['id'], $u['id'], $u['id']]
) : [];

include __DIR__.'/../includes/layout_top.php';
?>
<?php if($u['role']==='admin'): ?>
  <div class="card empty-state">Project coordinators use analytics and assignment tools. Direct project messaging is restricted to the assigned student and supervisor.</div>
<?php elseif(!$project || !$partner): ?>
  <div class="card empty-state">A linked project and supervision partner are required before messaging can begin.</div>
<?php else: ?>
  <?php if($u['role']==='supervisor' && count($projects)>1): ?>
  <div class="card compact-card"><form method="get" action="<?= e(url('index.php')) ?>" class="inline-form"><input type="hidden" name="page" value="messages"><div class="form-group grow"><label>Student project conversation</label><select name="project_id" onchange="this.form.submit()"><?php foreach($projects as $p): ?><option value="<?= (int)$p['id'] ?>" <?= (int)$p['id']===(int)$project['id']?'selected':'' ?>><?= e($p['student_name'].' — '.$p['title']) ?></option><?php endforeach; ?></select></div><noscript><button class="button small" type="submit">Open</button></noscript></form></div>
  <?php endif; ?>
  <div class="dashboard-grid">
    <section class="card"><div class="card-header"><div><h2>Conversation with <?= e($partner['full_name']) ?></h2><p><?= e($project['title']) ?></p></div></div>
      <?php if(!$msgs): ?><div class="empty-state">No messages yet.</div><?php else: foreach($msgs as $m): ?>
        <div class="message-bubble <?= (int)$m['sender_id']===(int)$u['id']?'mine':'' ?>"><strong><?= e($m['subject'] ?: 'Project update') ?></strong><br><?= nl2br(e($m['body'])) ?><small><?= e($m['sender_name']) ?> · <?= e(format_date($m['created_at'])) ?></small></div>
      <?php endforeach; endif; ?>
    </section>
    <aside class="card"><div class="card-header"><h3>Send a message</h3></div><form method="post" action="<?= e(url('actions.php')) ?>"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="send_message"><input type="hidden" name="project_id" value="<?= (int)$project['id'] ?>"><input type="hidden" name="receiver_id" value="<?= (int)$partner['id'] ?>"><div class="form-group"><label>Subject</label><input name="subject" maxlength="180" placeholder="Chapter review update"></div><div class="form-group"><label>Message</label><textarea name="body" maxlength="5000" required></textarea></div><button class="button" type="submit">Send message</button></form></aside>
  </div>
<?php endif; ?>
<?php include __DIR__.'/../includes/layout_bottom.php'; ?>
