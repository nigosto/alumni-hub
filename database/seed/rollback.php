<?php
require_once __DIR__ . "/../../database.php";

$database = new Database();
$db_name = $_ENV['DB_NAME'];
$db_con = $database->connection();

$db_con->exec(<<<CT
INSERT INTO Users (email, password, username, role)
VALUES
    ('ivan.petrov@example.com', 'parola123', 'IvanPetrov', 'admin'),
    ('maria.ivanova@example.com', 'qwerty123', 'MariaIvanova', 'administrator'),
    ('georgi.georgiev@example.com', 'pass456', 'GeorgiGeorgiev', 'administrator'),

    ('elena.stoyanova@example.com', 'secure789', 'ElenaStoyanova', 'student'),
    ('petar.kolev@example.com', 'abc12345', 'PetarKolev', 'student'),
    ('krasimira.dimitrova@example.com', 'password321', 'KrasimiraDimitrova', 'student'),

    ('nikolay.iliev@example.com', 'letmein123', 'NikolayIliev', 'student'),
    ('valentina.marinova@example.com', 'zdrasti123', 'ValentinaMarinova', 'student'),
    ('stefan.popov@example.com', 'pass7890', 'StefanPopov', 'student'),

    ('daniela.angelova@example.com', 'welcome123', 'DanielaAngelova', 'student');
    ('boris.kolev@example.com', 'student123', 'BorisKolev', 'student'),
    ('yana.vasileva@example.com', 'learning456', 'YanaVasileva', 'student'),
    
    ('mitko.dimitrov@example.com', 'booklover789', 'MitkoDimitrov', 'student'),
    ('teodora.petkova@example.com', 'studytime321', 'TeodoraPetkova', 'student'),
    ('alexander.stanev@example.com', 'studentpass2023', 'AlexanderStanev', 'student');
CT);

$db_con->exec(<<<CT
INSERT INTO Students (fn, degree, fullname, graduation_year, grade, user_id)
VALUES 
    ('0mi4', 'bachelor', 'Elena Andreeva Stoyanova', '2025', 3.01, 4),
    ('0mi5', 'bachelor', 'Petar Stanimirev Kolev', '2025', 6.00, 5),
    ('0mi6', 'bachelor', 'Krasimira Nikolaeva Dimitrova', '2025', 6.01, 6),

    ('0mi7', 'bachelor', 'Nikolay Radoslavov Iliev', '2025', 5.00, 7),
    ('0mi8', 'master', 'Valentina Valerieva Marinova', '2025', 5.50, 8),
    ('0mi9', 'bachelor', 'Stefan Konstantinov Popov', '2025', 5.00, 9),

    ('0mi10', 'bachelor', 'Daniela Plamenova Angelova', '2026', 5.00, 10),
    ('0mi11', 'bachelor', 'Boris Borisov Kolev', '2025', 5.50, 11),
    ('0mi12', 'doctor', 'Yana Pravdomirova Vasileva', '2025', 6.50, 12)

    ('0mi13', 'bachelor', 'Mitko Stanislavov Dimitrov', '2026', 5.00, 10),
    ('0mi14', 'bachelor', 'Teodora Vasileva Petkova', '2025', 5.75, 11),
    ('0mi15', 'doctor', Alexander Viktorov Stanev', '2025', 4.50, 12)
CT);

$db_con->exec(<<<CT
INSERT INTO Clothes (size, student_fn)
VALUES 
    ('S', '0mi4'),
    ('L', '0mi5'),
    ('M', '0mi6'),
    ('M', '0mi7'),
    ('XL','0mi8'),
    ('XL', '0mi9'),
    ('S', '0mi10'),
    ('S', '0mi11'),
    ('S', '0mi12'),
    ('M', '0mi13'),
    ('M', '0mi14'),
    ('L', '0mi15')
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
    ('2', '0mi4', FALSE, 'none', 'none'),
    ('2', '0mi5', NULL, 'none', 'none'),
    ('2', '0mi6', NULL, 'none', 'none'),
    ('2', '0mi7', NULL, 'none', 'none'),
    ('2', '0mi8', NULL, 'none', 'none'),
    ('2', '0mi11', NULL, 'none', 'none'),
    ('2', '0mi12', NULL, 'none', 'declined')
    ('2', '0mi14', NULL, 'none', 'declined')
    ('2', '0mi15', NULL, 'none', 'declined')
CT);

?>