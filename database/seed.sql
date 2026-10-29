USE supervisehub;
-- Password for all demo users: Password123!
INSERT INTO users (id,full_name,email,password_hash,role,matric_no,staff_no,department,status) VALUES
(1,'Adebayo Samuel','student@demo.test','$2y$12$MYDNPA.N14cy5.ddk/qRqevNj0F1mu7/oj3HqJx8/o1R.HAZFJE4W','student','CU/CSC/22/014',NULL,'Department of Computer Science','active'),
(2,'Dr. Grace Okafor','supervisor@demo.test','$2y$12$MYDNPA.N14cy5.ddk/qRqevNj0F1mu7/oj3HqJx8/o1R.HAZFJE4W','supervisor',NULL,'CU/CSC/042','Department of Computer Science','active'),
(3,'Project Coordinator','admin@demo.test','$2y$12$MYDNPA.N14cy5.ddk/qRqevNj0F1mu7/oj3HqJx8/o1R.HAZFJE4W','admin',NULL,'CU/CSC/001','Department of Computer Science','active'),
(4,'Chiamaka Nwosu','chiamaka@demo.test','$2y$12$MYDNPA.N14cy5.ddk/qRqevNj0F1mu7/oj3HqJx8/o1R.HAZFJE4W','student','CU/CSC/22/031',NULL,'Department of Computer Science','active'),
(5,'Ibrahim Musa','ibrahim@demo.test','$2y$12$MYDNPA.N14cy5.ddk/qRqevNj0F1mu7/oj3HqJx8/o1R.HAZFJE4W','student','CU/CSC/22/047',NULL,'Department of Computer Science','active'),
(6,'Dr. Michael Adekunle','michael@demo.test','$2y$12$MYDNPA.N14cy5.ddk/qRqevNj0F1mu7/oj3HqJx8/o1R.HAZFJE4W','supervisor',NULL,'CU/CSC/051','Department of Computer Science','active');

INSERT INTO projects (id,student_id,supervisor_id,title,abstract,current_stage,status,start_date,due_date) VALUES
(1,1,2,'Design and Implementation of Student/Supervisor Session for Project Monitoring and Progress','A web-based platform for structured supervision scheduling, feedback and milestone monitoring.','Implementation and Testing','active','2026-02-10','2026-09-15'),
(2,4,2,'Development of a Web-Based Laboratory Equipment Booking System',NULL,'Methodology','active','2026-02-10','2026-09-15'),
(3,5,6,'Design of an Intelligent Hostel Maintenance Reporting Platform',NULL,'Literature Review','active','2026-02-10','2026-09-15');

INSERT INTO milestones (project_id,title,description,due_date,weight,status,student_note,supervisor_comment,submitted_at,approved_at) VALUES
(1,'Topic and Proposal Approval','Complete and submit the approved topic and proposal.','2026-02-28',15,'approved','Proposal uploaded and corrected.','Topic is focused and implementable.','2026-02-22 10:00:00','2026-02-24 13:00:00'),
(1,'Chapter One','Background, problem, objectives, methodology, significance and scope.','2026-03-31',15,'approved','Revised Chapter One submitted.','Approved after correction of scope and references.','2026-03-28 09:00:00','2026-03-30 14:00:00'),
(1,'Literature Review','Conceptual, theoretical and empirical review.','2026-05-15',20,'approved','Expanded related works from 2020-2026.','Literature gap is now explicit.','2026-05-12 11:00:00','2026-05-14 15:00:00'),
(1,'Methodology and System Design','Requirements, architecture, diagrams and database design.','2026-06-30',20,'approved','ERD, DFD and use cases included.','Approved; maintain consistent table naming.','2026-06-27 12:00:00','2026-06-29 16:00:00'),
(1,'Implementation and Testing','PHP implementation, screenshots and test results.','2026-08-10',20,'submitted','Core role dashboards and black-box tests completed.',NULL,'2026-07-25 17:00:00',NULL),
(1,'Final Report and Defence','Complete report, appendices and defence preparation.','2026-09-10',10,'in_progress',NULL,NULL,NULL,NULL),
(2,'Topic and Proposal Approval','Approved topic and proposal.','2026-02-28',15,'approved',NULL,'Approved',NULL,'2026-02-25 12:00:00'),
(2,'Chapter One','Introductory chapter.','2026-03-31',15,'approved',NULL,'Approved',NULL,'2026-04-02 12:00:00'),
(2,'Literature Review','Related literature.','2026-05-15',20,'approved',NULL,'Approved',NULL,'2026-05-18 12:00:00'),
(2,'Methodology and System Design','Design artefacts.','2026-06-30',20,'submitted','System diagrams uploaded.',NULL,'2026-07-20 12:00:00',NULL),
(2,'Implementation and Testing','Application and tests.','2026-08-10',20,'pending',NULL,NULL,NULL,NULL),
(2,'Final Report and Defence','Final report.','2026-09-10',10,'pending',NULL,NULL,NULL,NULL),
(3,'Topic and Proposal Approval','Approved topic and proposal.','2026-02-28',15,'approved',NULL,'Approved',NULL,'2026-03-03 12:00:00'),
(3,'Chapter One','Introductory chapter.','2026-03-31',15,'approved',NULL,'Approved',NULL,'2026-04-10 12:00:00'),
(3,'Literature Review','Related literature.','2026-05-15',20,'revision','Draft submitted.','Expand Nigerian studies and correct citations.','2026-07-10 12:00:00',NULL),
(3,'Methodology and System Design','Design artefacts.','2026-06-30',20,'pending',NULL,NULL,NULL,NULL),
(3,'Implementation and Testing','Application and tests.','2026-08-10',20,'pending',NULL,NULL,NULL,NULL),
(3,'Final Report and Defence','Final report.','2026-09-10',10,'pending',NULL,NULL,NULL,NULL);

INSERT INTO sessions (project_id,requested_by,scheduled_at,mode,venue_or_link,agenda,status,supervisor_feedback,decisions,action_items,next_session_date,completed_at) VALUES
(1,1,'2026-03-12 10:00:00','physical','Office CSC-12','Review Chapter One problem statement and objectives.','completed','The problem is clear but the objectives must correspond directly to system modules.','Retain five objectives and specify the departmental context.','Correct objectives, standardise the institution name, and update references.','2026-03-26 10:00:00','2026-03-12 10:55:00'),
(1,1,'2026-05-08 09:30:00','online','Google Meet','Review literature structure and recent studies.','completed','The review should separate concepts from empirical systems and conclude with a precise gap.','Use literature published from 2020 to 2026.','Add at least ten directly related systems and a comparison table.','2026-05-22 09:30:00','2026-05-08 10:35:00'),
(1,1,'2026-06-19 11:00:00','physical','Software Laboratory','Validate use case, DFD, ERD and database schema.','completed','The design is coherent. Add role-based permissions and audit notifications.','Adopt PHP, PDO and MySQL with responsive HTML/CSS/JavaScript.','Implement authentication, sessions, milestones, documents, messages and analytics.','2026-07-03 11:00:00','2026-06-19 12:10:00'),
(1,1,'2026-07-30 10:00:00','hybrid','CSC-12 / Meet link','Demonstrate the completed application and testing evidence.','approved',NULL,NULL,NULL,NULL,NULL),
(2,4,'2026-07-29 13:00:00','physical','Office CSC-12','Review methodology corrections.','requested',NULL,NULL,NULL,NULL,NULL),
(3,5,'2026-07-31 09:00:00','online','Microsoft Teams','Discuss literature review revisions.','approved',NULL,NULL,NULL,NULL,NULL);

INSERT INTO messages(project_id,sender_id,receiver_id,subject,body,created_at) VALUES
(1,1,2,'Updated implementation','Good afternoon Ma. I have completed the session scheduling and milestone modules and uploaded the revised Chapter Three.','2026-07-24 14:12:00'),
(1,2,1,'Re: Updated implementation','Well done. Please ensure the dashboard progress is derived from approved milestones and prepare the black-box test table before our next session.','2026-07-24 16:05:00');
