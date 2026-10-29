<?php
return [
    'name' => getenv('APP_NAME') ?: 'SuperviseHub',
    'institution' => getenv('INSTITUTION_NAME') ?: 'Crawford University',
    'department' => getenv('DEPARTMENT_NAME') ?: 'Department of Computer Science',
    'base_url' => rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/'),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Africa/Lagos',
    'upload_dir' => dirname(__DIR__) . '/uploads',
    'max_upload_bytes' => 5 * 1024 * 1024,
];
