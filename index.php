<?php
require __DIR__ . '/includes/bootstrap.php';
$page = $_GET['page'] ?? (is_logged_in() ? dashboard_page(current_user()['role']) : 'home');
if ($page === 'logout') { session_destroy(); session_start(); flash('success','You have been signed out.'); redirect('index.php?page=login'); }
$publicPages = ['home','login','register'];
if (!in_array($page,$publicPages,true)) require_login();
$routes = [
 'home'=>'home.php','login'=>'login.php','register'=>'register.php',
 'student-dashboard'=>'student_dashboard.php','student-sessions'=>'student_sessions.php','student-milestones'=>'student_milestones.php','student-documents'=>'student_documents.php',
 'supervisor-dashboard'=>'supervisor_dashboard.php','supervisor-students'=>'supervisor_students.php','supervisor-project'=>'supervisor_project.php','supervisor-sessions'=>'supervisor_sessions.php','supervisor-milestones'=>'supervisor_milestones.php',
 'admin-dashboard'=>'admin_dashboard.php','admin-users'=>'admin_users.php','admin-assignments'=>'admin_assignments.php','admin-projects'=>'admin_projects.php','admin-reports'=>'admin_reports.php',
 'messages'=>'messages.php'
];
if (!isset($routes[$page])) { http_response_code(404); $page='home'; }
include __DIR__ . '/views/' . $routes[$page];
