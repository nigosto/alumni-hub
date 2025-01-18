<?php
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../../config.php";
load_config(".env");

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

    ('daniela.angelova@example.com', 'welcome123', 'DanielaAngelova', 'student'),
    ('boris.kolev@example.com', 'student123', 'BorisKolev', 'student'),
    ('yana.vasileva@example.com', 'learning456', 'YanaVasileva', 'student'),
    
    ('mitko.dimitrov@example.com', 'booklover789', 'MitkoDimitrov', 'student'),
    ('teodora.petkova@example.com', 'studytime321', 'TeodoraPetkova', 'student'),
    ('alexander.stanev@example.com', 'studentpass2023', 'AlexanderStanev', 'student');
CT);

$db_con->exec(<<<CT
INSERT INTO Students (fn, degree, fullname, graduation_year, grade, user_id)
VALUES 
    ('1MI1234567', 'bachelor', 'Елена Андреева Стоянова', '2025', 3.01, 4),
    ('2MI9876543', 'bachelor', 'Петър Станимирeв Колев', '2025', 6.00, 5),
    ('3MI4567890', 'bachelor', 'Красимира Николаева Димитрова', '2025', 6.01, 6),

    ('4MI6543210', 'bachelor', 'Николай Радославов Илиев', '2025', 5.00, 7),
    ('5MI2345678', 'master', 'Валентина Валериева Маринова', '2025', 5.50, 8),
    ('6MI8765432', 'bachelor', 'Стефан Константинов Попов', '2025', 5.00, 9),

    ('7MI3456789', 'bachelor', 'Даниела Пламенова Ангелова', '2026', 5.00, 10),
    ('8MI7654321', 'bachelor', 'Борис Борисов Колев', '2025', 5.50, 11),
    ('9MI5678901', 'doctor', 'Яна Правдомирова Василева', '2025', 6.50, 12),

    ('1MI8901234', 'bachelor', 'Митко Станиславов Димитров', '2026', 5.00, 10),
    ('2MI6789012', 'bachelor', 'Теодора Василева Петкова', '2025', 5.75, 11),
    ('3MI7890123', 'doctor', 'Александър Викторов Станев', '2025', 4.50, 12)
CT);

$db_con->exec(<<<CT
INSERT INTO Clothes (size, student_fn)
VALUES 
    ('S', '1MI1234567'),
    ('L', '2MI9876543'),
    ('M', '3MI4567890'),
    ('M', '4MI6543210'),
    ('XL', '5MI2345678'),
    ('XL', '6MI8765432'),
    ('S', '7MI3456789'),
    ('S', '8MI7654321'),
    ('S', '9MI5678901'),
    ('M', '1MI8901234'),
    ('M', '2MI6789012'),
    ('L', '3MI7890123')
CT);

$db_con->exec(<<<CT
INSERT INTO Ceremony (date)
VALUES 
    ('2024-02-19 10:30:00'),
    ('2025-02-23 10:00:00')
CT);

$db_con->exec(<<<CT
INSERT INTO Ceremony_Attendance (ceremony_id, student_fn, accepted, speach_status, responsibility_status)
VALUES 
    ('2', '1MI1234567', FALSE, 'none', 'none'),
    ('2', '2MI9876543', NULL, 'none', 'none'),
    ('2', '3MI4567890', NULL, 'none', 'none'),

    ('2', '4MI6543210', NULL, 'none', 'none'),
    ('2', '5MI2345678', NULL, 'none', 'accepted_robes'),
    ('2', '6MI8765432', NULL, 'none', 'accepted_robes'),

    ('2', '8MI7654321', NULL, 'none', 'declined_robes'),
    ('2', '9MI5678901', NULL, 'none', 'declined_robes'),
    ('2', '2MI6789012', NULL, 'none', 'declined_robes'),
    ('2', '3MI7890123', NULL, 'none', 'declined_robes')
CT);

?>