<?php
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string {
    global $config;
    return $config['base_url'] . ($path ? '/' . ltrim($path, '/') : '');
}
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function flash(string $type, string $message): void { $_SESSION['flash'][] = compact('type', 'message'); }
function pull_flashes(): array { $items = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $items; }
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419); exit('Your session expired. Please go back and try again.');
    }
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function is_logged_in(): bool { return current_user() !== null; }
function require_login(): void { if (!is_logged_in()) redirect('index.php?page=login'); }
function require_role(array|string $roles): void {
    require_login(); $roles = (array)$roles;
    if (!in_array(current_user()['role'], $roles, true)) { http_response_code(403); exit('Access denied.'); }
}
function dashboard_page(string $role): string {
    return match($role) {
        'student' => 'student-dashboard', 'supervisor' => 'supervisor-dashboard', default => 'admin-dashboard'
    };
}
function db_one(string $sql, array $params = []): ?array {
    global $pdo; $st = $pdo->prepare($sql); $st->execute($params); $r = $st->fetch(); return $r ?: null;
}
function db_all(string $sql, array $params = []): array {
    global $pdo; $st = $pdo->prepare($sql); $st->execute($params); return $st->fetchAll();
}
function db_exec(string $sql, array $params = []): int {
    global $pdo; $st = $pdo->prepare($sql); $st->execute($params); return $st->rowCount();
}
function format_date(?string $value, string $format = 'd M Y, h:i A'): string {
    if (!$value) return 'Not set'; return date($format, strtotime($value));
}
function status_class(string $status): string {
    return match(strtolower($status)) {
        'approved','completed','active','submitted' => 'success',
        'requested','pending','in progress','in_progress','rescheduled' => 'warning',
        'revision','cancelled','rejected','inactive','overdue' => 'danger',
        default => 'neutral'
    };
}
function progress_for_project(int $projectId): int {
    global $pdo;
    $st = $pdo->prepare("SELECT COALESCE(SUM(CASE WHEN status='approved' THEN weight ELSE 0 END),0) AS earned, COALESCE(SUM(weight),0) AS total FROM milestones WHERE project_id=?");
    $st->execute([$projectId]); $r = $st->fetch();
    if (!$r || (int)$r['total'] === 0) return 0;
    return min(100, (int)round(((float)$r['earned'] / (float)$r['total']) * 100));
}
function project_for_student(int $studentId): ?array {
    return db_one("SELECT p.*, u.full_name supervisor_name, u.email supervisor_email FROM projects p LEFT JOIN users u ON u.id=p.supervisor_id WHERE p.student_id=? ORDER BY p.id DESC LIMIT 1", [$studentId]);
}
function ensure_upload_dir(): void { global $config; if (!is_dir($config['upload_dir'])) mkdir($config['upload_dir'], 0775, true); }
function create_notification(int $userId, string $type, string $message, string $link=''): void {
    db_exec("INSERT INTO notifications (user_id,type,message,link,is_read,created_at) VALUES (?,?,?,?,0,CURRENT_TIMESTAMP)", [$userId,$type,$message,$link]);
}
function nav_active(string $page, string $target): string { return $page === $target ? 'active' : ''; }
