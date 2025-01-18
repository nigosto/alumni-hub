<?php
require_once __DIR__ . "/../../index.php";

$db_name = $_ENV['DB_NAME'];
$db_con = $database->connection();

$db_con->exec(<<<CT
INSERT INTO Users (email, password, username, role)
VALUES 
    ('sudo_udo@sudo.udo', 'pokemon', 'sudo_udo', 'admin'),
    ('student_admin@fmi-admin-sofia.bg', 'student_admin', 'student_admin', 'administrator'),
    ('maria87@fmi-admin-sofia.bg', 'secure_pass', 'maria87', 'administrator'),

    ('rosenandreevkolev1@fmi-sofia.bg', 'nacepeniq', 'MasterTroppical', 'student'),
    ('ralica02@fmi-sofia.bg', '20acilar', 'ralica02', 'student'),
    ('georgi_atanasow02@fmi-sofia.bg', 'go6olo6o', 'nigosto', 'student'),

    ('radoslav02@fmi-sofia.bg', 'radoto1', 'sirini', 'student'),
    ('petar01@fmi-sofia.bg', 'ma4kaigri6o', 'pe6o', 'student'),
    ('vj02@fmi-sofia.bg', 'mbt', 'jekata_jekov', 'student'),

    ('ng@fmi-sofia.bg', 'cukame_ma4', 'vulka', 'student'),
    ('viktor02@fmi-sofia.bg', 'kapra', 'demon', 'student'),
    ('kaloyan02@fmi-sofia.bg', 'sila_kala_i_respekt', 'gotin_sum', 'student')
CT);

$db_con->exec(<<<CT
INSERT INTO Students (fn, degree, fullname, graduation_year, grade, user_id)
VALUES 
    ('0mi4', 'bachelor', 'Rosen Andreev Kolev', '2025', 3.01, 4),
    ('0mi5', 'bachelor', 'Ralica Atanasova Simova', '2025', 6.00, 5),
    ('0mi6', 'bachelor', 'Georgi Nikolaev Atanasov', '2025', 6.01, 6),

    ('0mi7', 'bachelor', 'Radoslav Radoslav Karatanev', '2025', 5.00, 7),
    ('0mi8', 'master', 'Petar Stanimirev Kolev', '2025', 5.50, 8),
    ('0mi9', 'bachelor', 'Valeri Marsov Jekov', '2025', 5.00, 9),

    ('0mi10', 'bachelor', 'Nikolai Nikolai Georgiev', '2026', 5.00, 10),
    ('0mi11', 'bachelor', 'Viktor Viktor Kapra', '2025', 5.50, 11),
    ('0mi12', 'doctor', 'Kaloyan Kaloyan Tsvetkov', '2025', 6.50, 12)
CT);

$db_con->exec(<<<CT
INSERT INTO Clothes (size, student_fn)
VALUES 
    ('L', '0mi4'),
    ('S', '0mi5'),
    ('S', '0mi6'),
    ('M', '0mi7'),
    ('XL','0mi8'),
    ('S', '0mi9'),
    ('M', '0mi10'),
    ('L', '0mi11'),
    ('M', '0mi12')
CT);

$db_con->exec(<<<CT
INSERT INTO Ceremony (date)
VALUES 
    ('2024-02-19 10:30:00'),
    ('2025-02-23 10:00:00')
CT);

$db_con->exec(<<<CT
INSERT INTO Ceremony_Attendance (ceremony_id, student_fn, accepted, speach_status, boss_status)
VALUES 
    ('2', '0mi4', NULL, 'none', 'none'),
    ('2', '0mi5', NULL, 'none', 'none'),
    ('2', '0mi6', NULL, 'none', 'none'),
    ('2', '0mi7', NULL, 'none', 'none'),
    ('2', '0mi8', NULL, 'none', 'none'),
    ('2', '0mi10', NULL, 'none', 'none'),
    ('2', '0mi11', NULL, 'none', 'none'),
    ('2', '0mi12', NULL, 'none', 'declined')
CT);

?>