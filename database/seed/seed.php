<?php
require_once __DIR__ . "/../database.php";
require_once __DIR__ . "/../../config.php";
load_config(".env");

$database = new Database();
$db_name = $_ENV['DB_NAME'];
$db_con = $database->connection();

$hashed_passwords = [
    'parola123',
    'qwerty123',
    'pass456',
    'secure789',
    'abc12345',
    'password321',
    'letmein123',
    'zdrasti123',
    'pass7890',
    'welcome123',
    'student123',
    'learning456',
    'booklover789',
    'studytime321',
    'studentpass2023'
];

$hashed_passwords = array_map(fn($value): string => password_hash($value, PASSWORD_DEFAULT), $hashed_passwords);

$db_con->exec(<<<CT
INSERT INTO Users (email, password, username, role)
VALUES
    ('ivan.petrov@example.com', '{$hashed_passwords[0]}', 'IvanPetrov', 'admin'),
    ('maria.ivanova@example.com', '{$hashed_passwords[1]}', 'MariaIvanova', 'administrator'),
    ('georgi.georgiev@example.com', '{$hashed_passwords[2]}', 'GeorgiGeorgiev', 'administrator'),

    ('elena.stoyanova@example.com', '{$hashed_passwords[3]}', 'ElenaStoyanova', 'student'),
    ('petar.kolev@example.com', '{$hashed_passwords[4]}', 'PetarKolev', 'student'),
    ('krasimira.dimitrova@example.com', '{$hashed_passwords[5]}', 'KrasimiraDimitrova', 'student'),

    ('nikolay.iliev@example.com', '{$hashed_passwords[6]}', 'NikolayIliev', 'student'),
    ('valentina.marinova@example.com', '{$hashed_passwords[7]}', 'ValentinaMarinova', 'student'),
    ('stefan.popov@example.com', '{$hashed_passwords[8]}', 'StefanPopov', 'student'),

    ('daniela.angelova@example.com', '{$hashed_passwords[9]}', 'DanielaAngelova', 'student'),
    ('boris.kolev@example.com', '{$hashed_passwords[10]}', 'BorisKolev', 'student'),
    ('yana.vasileva@example.com', '{$hashed_passwords[11]}', 'YanaVasileva', 'student'),
    
    ('mitko.dimitrov@example.com', '{$hashed_passwords[12]}', 'MitkoDimitrov', 'student'),
    ('teodora.petkova@example.com', '{$hashed_passwords[13]}', 'TeodoraPetkova', 'student'),
    ('alexander.stanev@example.com', '{$hashed_passwords[14]}', 'AlexanderStanev', 'student');
CT);

$db_con->exec(<<<CT
INSERT INTO Students (fn, degree, fullname, graduation_year, grade)
VALUES 
    ('1MI1234567', 'bachelor', 'Елена Андреева Стоянова', '2025', 3.01),
    ('2MI9876543', 'bachelor', 'Петър Станимирeв Колев', '2025', 6.00),
    ('3MI4567890', 'bachelor', 'Красимира Николаева Димитрова', '2025', 6.01),

    ('4MI6543210', 'bachelor', 'Николай Радославов Илиев', '2025', 5.00),
    ('5MI2345678', 'master', 'Валентина Валериева Маринова', '2025', 5.50),
    ('6MI8765432', 'bachelor', 'Стефан Константинов Попов', '2025', 5.00),

    ('7MI3456789', 'bachelor', 'Даниела Пламенова Ангелова', '2026', 5.00),
    ('8MI7654321', 'bachelor', 'Борис Борисов Колев', '2025', 5.50),
    ('9MI5678901', 'doctor', 'Яна Правдомирова Василева', '2025', 6.50),

    ('1MI8901234', 'bachelor', 'Митко Станиславов Димитров', '2026', 5.00),
    ('2MI6789012', 'bachelor', 'Теодора Василева Петкова', '2025', 5.75),
    ('3MI7890123', 'doctor', 'Александър Викторов Станев', '2025', 4.50)
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