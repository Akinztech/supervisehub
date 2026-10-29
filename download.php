<?php
require __DIR__ . '/includes/bootstrap.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$document = db_one(
    "SELECT d.*, p.student_id, p.supervisor_id
     FROM documents d
     JOIN projects p ON p.id = d.project_id
     WHERE d.id = ?",
    [$id]
);

if (!$document) {
    http_response_code(404);
    exit('Document not found.');
}

$user = current_user();
$authorised = $user['role'] === 'admin'
    || ($user['role'] === 'student' && (int)$document['student_id'] === (int)$user['id'])
    || ($user['role'] === 'supervisor' && (int)$document['supervisor_id'] === (int)$user['id']);

if (!$authorised) {
    http_response_code(403);
    exit('Access denied.');
}

$uploadRoot = realpath($config['upload_dir']);
$file = $uploadRoot ? realpath($uploadRoot . DIRECTORY_SEPARATOR . basename($document['stored_name'])) : false;
if (!$uploadRoot || !$file || !str_starts_with($file, $uploadRoot . DIRECTORY_SEPARATOR) || !is_file($file)) {
    http_response_code(404);
    exit('The stored file is unavailable.');
}

$extension = strtolower(pathinfo($document['original_name'], PATHINFO_EXTENSION));
$mime = match ($extension) {
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    default => 'application/octet-stream',
};

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($file));
header("Content-Disposition: inline; filename*=UTF-8''" . rawurlencode($document['original_name']));
header('X-Content-Type-Options: nosniff');
readfile($file);
exit;
