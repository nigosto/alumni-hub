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
INSERT INTO Users (email, password, username, role, approved)
VALUES
    ('ivan.petrov@example.com', '{$hashed_passwords[0]}', 'IvanPetrov', 'admin', 1),
    ('maria.ivanova@example.com', '{$hashed_passwords[1]}', 'MariaIvanova', 'administrator', 1),
    ('georgi.georgiev@example.com', '{$hashed_passwords[2]}', 'GeorgiGeorgiev', 'administrator', 0),

    ('elena.stoyanova@example.com', '{$hashed_passwords[3]}', 'ElenaStoyanova', 'student', 1),
    ('petar.kolev@example.com', '{$hashed_passwords[4]}', 'PetarKolev', 'student', 1),
    ('krasimira.dimitrova@example.com', '{$hashed_passwords[5]}', 'KrasimiraDimitrova', 'student', 1),

    ('nikolay.iliev@example.com', '{$hashed_passwords[6]}', 'NikolayIliev', 'student', 1),
    ('valentina.marinova@example.com', '{$hashed_passwords[7]}', 'ValentinaMarinova', 'student', 1),
    ('stefan.popov@example.com', '{$hashed_passwords[8]}', 'StefanPopov', 'student', 1),

    ('daniela.angelova@example.com', '{$hashed_passwords[9]}', 'DanielaAngelova', 'student', 1),
    ('boris.kolev@example.com', '{$hashed_passwords[10]}', 'BorisKolev', 'student', 1),
    ('yana.vasileva@example.com', '{$hashed_passwords[11]}', 'YanaVasileva', 'student', 1),
    
    ('mitko.dimitrov@example.com', '{$hashed_passwords[12]}', 'MitkoDimitrov', 'student', 1),
    ('teodora.petkova@example.com', '{$hashed_passwords[13]}', 'TeodoraPetkova', 'student', 1),
    ('alexander.stanev@example.com', '{$hashed_passwords[14]}', 'AlexanderStanev', 'student', 1);
CT);

$db_con->exec(<<<CT
INSERT INTO Students (fn, degree, fullname, graduation_year, grade)
VALUES 
    ('1MI1234567', 'bachelor', 'Елена Андреева Стоянова', '2025', 3.01),
    ('2MI9876543', 'bachelor', 'Петър Станимирeв Колев', '2025', 6.00),
    ('3MI4567890', 'bachelor', 'Красимира Николаева Димитрова', '2025', 6.00),

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
INSERT INTO Clothes (size)
VALUES 
    ('S'),
    ('L'),
    ('M'),
    ('M'),
    ('XL'),
    ('XL'),
    ('S'),
    ('S'),
    ('S'),
    ('M'),
    ('M'),
    ('L')
CT);