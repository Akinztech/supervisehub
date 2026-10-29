CREATE DATABASE IF NOT EXISTS supervisehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE supervisehub;

CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(120) NOT NULL,
 email VARCHAR(160) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role VARCHAR(20) NOT NULL,
 matric_no VARCHAR(50) NULL UNIQUE,
 staff_no VARCHAR(50) NULL UNIQUE,
 department VARCHAR(160) NULL,
 status VARCHAR(20) NOT NULL DEFAULT 'active',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_users_role_status(role,status)
) ENGINE=InnoDB;

CREATE TABLE projects (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 student_id INT UNSIGNED NOT NULL,
 supervisor_id INT UNSIGNED NULL,
 title VARCHAR(300) NOT NULL,
 abstract TEXT NULL,
 current_stage VARCHAR(100) NOT NULL DEFAULT 'Proposal',
 status VARCHAR(30) NOT NULL DEFAULT 'active',
 start_date DATE NULL,
 due_date DATE NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_student_project(student_id),
 CONSTRAINT fk_projects_student FOREIGN KEY(student_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_projects_supervisor FOREIGN KEY(supervisor_id) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_projects_supervisor(supervisor_id), INDEX idx_projects_status(status)
) ENGINE=InnoDB;

CREATE TABLE sessions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 project_id INT UNSIGNED NOT NULL,
 requested_by INT UNSIGNED NOT NULL,
 scheduled_at DATETIME NOT NULL,
 mode VARCHAR(20) NOT NULL DEFAULT 'physical',
 venue_or_link VARCHAR(255) NULL,
 agenda TEXT NOT NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'requested',
 supervisor_feedback TEXT NULL,
 decisions TEXT NULL,
 action_items TEXT NULL,
 next_session_date DATETIME NULL,
 completed_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_sessions_project FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
 CONSTRAINT fk_sessions_requester FOREIGN KEY(requested_by) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_sessions_project_status(project_id,status), INDEX idx_sessions_scheduled(scheduled_at)
) ENGINE=InnoDB;

CREATE TABLE milestones (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 project_id INT UNSIGNED NOT NULL,
 title VARCHAR(180) NOT NULL,
 description TEXT NULL,
 due_date DATE NULL,
 weight INT NOT NULL DEFAULT 0,
 status VARCHAR(30) NOT NULL DEFAULT 'pending',
 student_note TEXT NULL,
 supervisor_comment TEXT NULL,
 submitted_at DATETIME NULL,
 approved_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_milestones_project FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
 INDEX idx_milestones_project_status(project_id,status), INDEX idx_milestones_due(due_date)
) ENGINE=InnoDB;

CREATE TABLE documents (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 project_id INT UNSIGNED NOT NULL,
 uploaded_by INT UNSIGNED NOT NULL,
 milestone_id INT UNSIGNED NULL,
 original_name VARCHAR(255) NOT NULL,
 stored_name VARCHAR(255) NOT NULL,
 file_path VARCHAR(500) NOT NULL,
 file_size INT UNSIGNED NOT NULL DEFAULT 0,
 version_no INT UNSIGNED NOT NULL DEFAULT 1,
 description TEXT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_documents_project FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
 CONSTRAINT fk_documents_uploader FOREIGN KEY(uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_documents_milestone FOREIGN KEY(milestone_id) REFERENCES milestones(id) ON DELETE SET NULL,
 INDEX idx_documents_project(project_id)
) ENGINE=InnoDB;

CREATE TABLE messages (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 project_id INT UNSIGNED NOT NULL,
 sender_id INT UNSIGNED NOT NULL,
 receiver_id INT UNSIGNED NOT NULL,
 subject VARCHAR(180) NULL,
 body TEXT NOT NULL,
 read_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_messages_project FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
 CONSTRAINT fk_messages_sender FOREIGN KEY(sender_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_messages_receiver FOREIGN KEY(receiver_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_messages_thread(project_id,sender_id,receiver_id)
) ENGINE=InnoDB;

CREATE TABLE notifications (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 type VARCHAR(40) NOT NULL,
 message VARCHAR(500) NOT NULL,
 link VARCHAR(500) NULL,
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_notifications_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_notifications_user_read(user_id,is_read)
) ENGINE=InnoDB;
