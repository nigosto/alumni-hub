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

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/profile/styles.css"],
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
$user = $controller->get_user();
$role = $_SESSION["role"];
?>

<body>
    <?php echo $header->render(); ?>

    <main id="container">
        <h2 id="welcome-heading">Добре дошли в Alumni Hub</h2>
        <p>Тук можете да намерите информация за вашия профил</p>
        <p class="entry"><strong class="entry-name">Потребителско име:</strong>
            <?= htmlspecialchars($user->get_username()) ?></p>
        <p class="entry"><strong class="entry-name">Имейл:</strong> <?= htmlspecialchars($user->get_email()) ?></p>

        <?php
        if ($role === "Student") {
            $fn = $_SESSION["fn"];
            $student = $controller->students_service->get_student_by_fn($fn);
            if ($student !== null) {
                $fullname = $student->get_fullname();
                $degree = prettify_degree($student->get_degree());
                $fn = $student->get_fn();
                $grade = $student->get_grade();
                $graduation_year = $student->get_graduation_year();

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