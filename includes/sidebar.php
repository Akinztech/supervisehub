<?php $role=$user['role']; ?>
<nav class="side-nav">
<?php if ($role==='student'): ?>
<a class="<?= nav_active($page,'student-dashboard') ?>" href="<?= e(url('index.php?page=student-dashboard')) ?>">Overview</a>
<a class="<?= nav_active($page,'student-sessions') ?>" href="<?= e(url('index.php?page=student-sessions')) ?>">Supervision Sessions</a>
<a class="<?= nav_active($page,'student-milestones') ?>" href="<?= e(url('index.php?page=student-milestones')) ?>">Milestones</a>
<a class="<?= nav_active($page,'student-documents') ?>" href="<?= e(url('index.php?page=student-documents')) ?>">Documents</a>
<a class="<?= nav_active($page,'messages') ?>" href="<?= e(url('index.php?page=messages')) ?>">Messages</a>
<?php elseif ($role==='supervisor'): ?>
<a class="<?= nav_active($page,'supervisor-dashboard') ?>" href="<?= e(url('index.php?page=supervisor-dashboard')) ?>">Overview</a>
<a class="<?= nav_active($page,'supervisor-students') ?>" href="<?= e(url('index.php?page=supervisor-students')) ?>">My Students</a>
<a class="<?= nav_active($page,'supervisor-sessions') ?>" href="<?= e(url('index.php?page=supervisor-sessions')) ?>">Session Requests</a>
<a class="<?= nav_active($page,'supervisor-milestones') ?>" href="<?= e(url('index.php?page=supervisor-milestones')) ?>">Milestone Reviews</a>
<a class="<?= nav_active($page,'messages') ?>" href="<?= e(url('index.php?page=messages')) ?>">Messages</a>
<?php else: ?>
<a class="<?= nav_active($page,'admin-dashboard') ?>" href="<?= e(url('index.php?page=admin-dashboard')) ?>">Analytics</a>
<a class="<?= nav_active($page,'admin-users') ?>" href="<?= e(url('index.php?page=admin-users')) ?>">Users</a>
<a class="<?= nav_active($page,'admin-assignments') ?>" href="<?= e(url('index.php?page=admin-assignments')) ?>">Assignments</a>
<a class="<?= nav_active($page,'admin-projects') ?>" href="<?= e(url('index.php?page=admin-projects')) ?>">Projects</a>
<a class="<?= nav_active($page,'admin-reports') ?>" href="<?= e(url('index.php?page=admin-reports')) ?>">Reports</a>
<?php endif; ?>
</nav>
