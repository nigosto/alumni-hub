<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../models/user.php";

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();

$user = $controller->get_user();
$user_data = $user->to_array();
$username = $user_data["username"];
$email = $user_data["email"];
$role = $_SESSION["role"];

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/profile/styles.css"],
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
    <?php echo $header->render(); ?>

    <main id="container">
        <h2 id="welcome-heading">Добре дошли в Alumni Hub</h2>
        <p>Тук можете да намерите информация за вашия профил</p>

        <?php
        echo <<<HTML
        <p class="entry"><strong class="entry-name">Потребителско име:</strong>
          $username</p>
        <p class="entry"><strong class="entry-name">Имейл:</strong> $email</p>
    HTML;

        if ($role === "Student") {
            $fn = $_SESSION["fn"];
            $student = $controller->students_service->get_student_by_fn($fn);
            if ($student !== null) {
                $student_data = $student->to_array(true);
                $fullname = $student_data["fullname"];
                $degree = $student_data["degree"];
                $fn = $student_data["fn"];
                $grade = $student_data["grade"];
                $graduation_year = $student_data["graduation_year"];

                echo <<<HTML
                <p class="entry"><strong class="entry-name">Име:</strong> $fullname</p>
                <p  class="entry"><strong  class="entry-name">Степен на образование:</strong>$degree</p>
                <p  class="entry"><strong  class="entry-name">Факултетен номер:</strong> $fn</p>
            <p  class="entry"><strong  class="entry-name">Оценка:</strong> $grade</p>
            <p  class="entry"><strong  class="entry-name">Година на завършване:</strong> $graduation_year</p>
            HTML;
            }
        }
        ?>
    </main>

    <?php echo $footer->render(); ?>
</body>

</html>