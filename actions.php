<?php
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();
$action = $_POST['action'] ?? '';
try {
switch ($action) {
case 'login':
    $email = strtolower(trim($_POST['email'] ?? '')); $password = $_POST['password'] ?? '';
    $user = db_one("SELECT * FROM users WHERE email=? AND status='active'", [$email]);
    if (!$user || !password_verify($password,$user['password_hash'])) { flash('danger','Invalid email address or password.'); redirect('index.php?page=login'); }
    unset($user['password_hash']); $_SESSION['user']=$user; session_regenerate_id(true); flash('success','Welcome back, '.$user['full_name'].'.'); redirect('index.php?page='.dashboard_page($user['role']));
case 'register':
    $name=trim($_POST['full_name']??''); $email=strtolower(trim($_POST['email']??'')); $matric=trim($_POST['matric_no']??''); $password=$_POST['password']??'';
    if (!$name || !$matric || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<8) { flash('danger','Provide a valid name, matriculation number, email and password of at least 8 characters.'); redirect('index.php?page=register'); }
    if (db_one('SELECT id FROM users WHERE email=?',[$email])) { flash('danger','An account already exists with that email.'); redirect('index.php?page=register'); }
    db_exec("INSERT INTO users (full_name,email,password_hash,role,matric_no,department,status,created_at) VALUES (?,?,?,?,?,?,?,CURRENT_TIMESTAMP)",[$name,$email,password_hash($password,PASSWORD_DEFAULT),'student',$matric,$config['department'],'active']);
    flash('success','Account created successfully. You may now sign in.'); redirect('index.php?page=login');
case 'request_session':
    require_role('student'); $project=project_for_student((int)current_user()['id']);
    if(!$project){ flash('danger','No project has been assigned to your account.'); redirect('index.php?page=student-sessions'); }
    $scheduled=trim($_POST['scheduled_at']??''); $mode=$_POST['mode']??'physical'; $agenda=trim($_POST['agenda']??''); $venue=trim($_POST['venue_or_link']??'');
    if(!$scheduled||!$agenda){ flash('danger','Session date and agenda are required.'); redirect('index.php?page=student-sessions'); }
    db_exec("INSERT INTO sessions(project_id,requested_by,scheduled_at,mode,venue_or_link,agenda,status,created_at) VALUES(?,?,?,?,?,?,'requested',CURRENT_TIMESTAMP)",[$project['id'],current_user()['id'],$scheduled,$mode,$venue,$agenda]);
    if($project['supervisor_id']) create_notification((int)$project['supervisor_id'],'session','New supervision session requested by '.current_user()['full_name'],'index.php?page=supervisor-sessions');
    flash('success','Session request submitted to your supervisor.'); redirect('index.php?page=student-sessions');
case 'session_decision':
    require_role('supervisor'); $id=(int)($_POST['session_id']??0); $status=$_POST['status']??'approved'; $newDate=trim($_POST['scheduled_at']??''); $venue=trim($_POST['venue_or_link']??'');
    if (!in_array($status,['approved','rescheduled','rejected','cancelled'],true)) throw new RuntimeException('Invalid session decision.');
    $s=db_one("SELECT s.*,p.student_id FROM sessions s JOIN projects p ON p.id=s.project_id WHERE s.id=? AND p.supervisor_id=?",[$id,current_user()['id']]);
    if(!$s) throw new RuntimeException('Session not found.');
    db_exec("UPDATE sessions SET status=?,scheduled_at=COALESCE(NULLIF(?,''),scheduled_at),venue_or_link=COALESCE(NULLIF(?,''),venue_or_link),updated_at=CURRENT_TIMESTAMP WHERE id=?",[$status,$newDate,$venue,$id]);
    create_notification((int)$s['student_id'],'session','Your supervision session was '.str_replace('_',' ',$status).'.','index.php?page=student-sessions');
    flash('success','Session status updated.'); redirect('index.php?page=supervisor-sessions');
case 'complete_session':
    require_role('supervisor'); $id=(int)($_POST['session_id']??0); $feedback=trim($_POST['supervisor_feedback']??''); $decisions=trim($_POST['decisions']??''); $actions=trim($_POST['action_items']??''); $next=trim($_POST['next_session_date']??'');
    $s=db_one("SELECT s.*,p.student_id FROM sessions s JOIN projects p ON p.id=s.project_id WHERE s.id=? AND p.supervisor_id=?",[$id,current_user()['id']]);
    if(!$s) throw new RuntimeException('Session not found.');
    db_exec("UPDATE sessions SET status='completed',supervisor_feedback=?,decisions=?,action_items=?,next_session_date=NULLIF(?,''),completed_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=?",[$feedback,$decisions,$actions,$next,$id]);
    create_notification((int)$s['student_id'],'feedback','New supervision feedback has been recorded.','index.php?page=student-sessions');
    flash('success','Session completed and feedback saved.'); redirect('index.php?page=supervisor-sessions');
case 'submit_milestone':
    require_role('student'); $id=(int)($_POST['milestone_id']??0); $note=trim($_POST['student_note']??'');
    $m=db_one("SELECT m.* FROM milestones m JOIN projects p ON p.id=m.project_id WHERE m.id=? AND p.student_id=?",[$id,current_user()['id']]); if(!$m) throw new RuntimeException('Milestone not found.');
    db_exec("UPDATE milestones SET status='submitted',student_note=?,submitted_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=?",[$note,$id]);
    $p=db_one('SELECT supervisor_id FROM projects WHERE id=?',[$m['project_id']]); if($p&&$p['supervisor_id']) create_notification((int)$p['supervisor_id'],'milestone','A milestone has been submitted for review.','index.php?page=supervisor-milestones');
    flash('success','Milestone submitted for review.'); redirect('index.php?page=student-milestones');
case 'review_milestone':
    require_role('supervisor'); $id=(int)($_POST['milestone_id']??0); $status=$_POST['status']??'revision'; $comment=trim($_POST['supervisor_comment']??'');
    if (!in_array($status,['approved','revision'],true)) throw new RuntimeException('Invalid milestone decision.');
    $m=db_one("SELECT m.*,p.student_id FROM milestones m JOIN projects p ON p.id=m.project_id WHERE m.id=? AND p.supervisor_id=?",[$id,current_user()['id']]); if(!$m) throw new RuntimeException('Milestone not found.');
    db_exec("UPDATE milestones SET status=?,supervisor_comment=?,approved_at=CASE WHEN ?='approved' THEN CURRENT_TIMESTAMP ELSE approved_at END,updated_at=CURRENT_TIMESTAMP WHERE id=?",[$status,$comment,$status,$id]);
    create_notification((int)$m['student_id'],'milestone','Milestone "'.$m['title'].'" was marked '.str_replace('_',' ',$status).'.','index.php?page=student-milestones');
    flash('success','Milestone review saved.'); redirect('index.php?page=supervisor-milestones');
case 'upload_document':
    require_role('student'); $project=project_for_student((int)current_user()['id']); if(!$project) throw new RuntimeException('No project assigned.');
    if(empty($_FILES['document'])||$_FILES['document']['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Select a valid document.');
    $file=$_FILES['document']; if($file['size']>$config['max_upload_bytes']) throw new RuntimeException('File exceeds the 5 MB limit.');
    $allowed=['pdf','doc','docx']; $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION)); if(!in_array($ext,$allowed,true)) throw new RuntimeException('Only PDF, DOC and DOCX files are allowed.');
    ensure_upload_dir(); $stored=bin2hex(random_bytes(12)).'.'.$ext; move_uploaded_file($file['tmp_name'],$config['upload_dir'].'/'.$stored);
    $version=(int)(db_one('SELECT COALESCE(MAX(version_no),0)+1 v FROM documents WHERE project_id=?',[$project['id']])['v']??1);
    db_exec("INSERT INTO documents(project_id,uploaded_by,original_name,stored_name,file_path,file_size,version_no,description,created_at) VALUES(?,?,?,?,?,?,?,?,CURRENT_TIMESTAMP)",[$project['id'],current_user()['id'],$file['name'],$stored,'uploads/'.$stored,$file['size'],$version,trim($_POST['description']??'')]);
    if($project['supervisor_id']) create_notification((int)$project['supervisor_id'],'document','A new project document was uploaded.','index.php?page=supervisor-students');
    flash('success','Document uploaded successfully.'); redirect('index.php?page=student-documents');
case 'send_message':
    require_role(['student','supervisor']);
    $projectId=(int)($_POST['project_id']??0); $receiver=(int)($_POST['receiver_id']??0); $subject=trim($_POST['subject']??''); $body=trim($_POST['body']??'');
    if(!$projectId||!$receiver||!$body) throw new RuntimeException('Recipient and message are required.');
    if(strlen($body)>5000 || strlen($subject)>180) throw new RuntimeException('The message is too long.');
    $project=db_one('SELECT student_id,supervisor_id FROM projects WHERE id=?',[$projectId]);
    if(!$project) throw new RuntimeException('Project not found.');
    $sender=(int)current_user()['id'];
    $valid=current_user()['role']==='student'
        ? ((int)$project['student_id']===$sender && (int)$project['supervisor_id']===$receiver)
        : ((int)$project['supervisor_id']===$sender && (int)$project['student_id']===$receiver);
    if(!$valid) throw new RuntimeException('You are not authorised to message this project participant.');
    db_exec("INSERT INTO messages(project_id,sender_id,receiver_id,subject,body,created_at) VALUES(?,?,?,?,?,CURRENT_TIMESTAMP)",[$projectId,$sender,$receiver,$subject,$body]);
    create_notification($receiver,'message','New message from '.current_user()['full_name'],'index.php?page=messages&project_id='.$projectId); flash('success','Message sent.'); redirect('index.php?page=messages&project_id='.$projectId);
case 'create_user':
    require_role('admin'); $name=trim($_POST['full_name']??'');$email=strtolower(trim($_POST['email']??''));$role=$_POST['role']??'student';$identifier=trim($_POST['identifier']??'');
    if(!$name||!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Valid name and email are required.');
    if(!in_array($role,['student','supervisor','admin'],true)) throw new RuntimeException('Invalid account role.');
    db_exec("INSERT INTO users(full_name,email,password_hash,role,matric_no,staff_no,department,status,created_at) VALUES(?,?,?,?,?,?,?,?,CURRENT_TIMESTAMP)",[$name,$email,password_hash('Password123!',PASSWORD_DEFAULT),$role,$role==='student'?$identifier:null,$role!=='student'?$identifier:null,$config['department'],'active']);
    flash('success','User created. Temporary password: Password123!'); redirect('index.php?page=admin-users');
case 'assign_supervisor':
    require_role('admin'); $student=(int)($_POST['student_id']??0);$supervisor=(int)($_POST['supervisor_id']??0);$title=trim($_POST['title']??'');$due=trim($_POST['due_date']??'');
    if(!$student||!$supervisor||!$title) throw new RuntimeException('Student, supervisor and project title are required.');
    if(!db_one("SELECT id FROM users WHERE id=? AND role='student' AND status='active'",[$student])) throw new RuntimeException('Valid active student not found.');
    if(!db_one("SELECT id FROM users WHERE id=? AND role='supervisor' AND status='active'",[$supervisor])) throw new RuntimeException('Valid active supervisor not found.');
    $pdo->beginTransaction();
    try {
      $existing=db_one('SELECT id FROM projects WHERE student_id=?',[$student]);
      if($existing) {
        db_exec("UPDATE projects SET supervisor_id=?,title=?,due_date=NULLIF(?,''),status='active',updated_at=CURRENT_TIMESTAMP WHERE id=?",[$supervisor,$title,$due,$existing['id']]);
      } else {
        db_exec("INSERT INTO projects(student_id,supervisor_id,title,current_stage,status,start_date,due_date,created_at) VALUES(?,?,?,'Proposal','active',CURRENT_DATE,NULLIF(?,''),CURRENT_TIMESTAMP)",[$student,$supervisor,$title,$due]);
        $projectId=(int)$pdo->lastInsertId(); $templates=[['Topic and Proposal Approval',15],['Chapter One',15],['Literature Review',20],['Methodology and System Design',20],['Implementation and Testing',20],['Final Report and Defence',10]];
        foreach($templates as [$t,$w]) db_exec("INSERT INTO milestones(project_id,title,description,due_date,weight,status,created_at) VALUES(?,?,?,NULL,?,'pending',CURRENT_TIMESTAMP)",[$projectId,$t,'Complete and submit '.$t.' for supervisor review.',$w]);
      }
      create_notification($student,'assignment','A supervisor and project record have been assigned to you.','index.php?page=student-dashboard');
      create_notification($supervisor,'assignment','A new student has been assigned to you.','index.php?page=supervisor-dashboard');
      $pdo->commit();
    } catch (Throwable $inner) { if($pdo->inTransaction()) $pdo->rollBack(); throw $inner; }
    flash('success','Supervisor assignment saved.'); redirect('index.php?page=admin-assignments');
case 'toggle_user':
    require_role('admin'); $id=(int)($_POST['user_id']??0);$status=$_POST['status']==='inactive'?'inactive':'active'; db_exec('UPDATE users SET status=? WHERE id=?',[$status,$id]); flash('success','User status updated.'); redirect('index.php?page=admin-users');
default: throw new RuntimeException('Unknown action.');
}
} catch (Throwable $e) { flash('danger',$e->getMessage()); $ref=$_SERVER['HTTP_REFERER']??''; $base=url().'/'; $target=str_starts_with($ref,$base)?substr($ref,strlen($base)):'index.php'; redirect($target); }
