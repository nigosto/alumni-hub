<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../models/user.php";
require_once __DIR__ . "/../../components/button/link.php";
require_once __DIR__ . "/../../components/message/message_component.php";

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();
$message = new MessageComponent();

$user = $controller->get_user();
$user_data = $user->to_array();
$username = $user_data["username"];
$email = $user_data["email"];
$role = $_SESSION["role"];

$user_id = $_SESSION["id"];
$fn = $_SESSION["fn"];
$clothing_for_student = $controller->clothes_service->get_clothing_for_student($fn);
$clothes = $controller->clothes_service->get_clothes_with_size();

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/profile/styles.css"],
    [$base_url . "/components/styles/input.css"],
    ButtonComponent::get_stylesheets(),
    MessageComponent::get_stylesheets()
);

$meta = new MetadataComponent($stylesheets, array_merge(
    MessageComponent::get_scripts(),
    ["$base_url/pages/profile/script.js"]
));
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

        if ($role === Role::Student) {
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

                if ($clothing_for_student !== null) {
                    echo <<<HTML
                        <p class="entry" ><strong class="entry-name">Размер на тога:</strong>
                        {$clothing_for_student->to_array()["size"]}</p>
                    HTML;
                } else {
                    echo <<<HTML
            <div class="entry" >
                <p><strong class="entry-name">Размер на тога:</strong></p>
                <form id="clothes">
                    <select name="pick-size" id="pick-size">
                        <option value="" disabled selected>Избор</option>
            HTML;
                    foreach ($clothes as $size => $occurrences) {
                        $is_disabled = $occurrences === 0;
                        if ($is_disabled) {
                            echo <<<HTML
                            <option value="{$size}" disabled> $size </option>
                        HTML;
                        } else {
                            echo <<<HTML
                            <option value="{$size}"> $size </option>
                        HTML;
                        }
                    }
                    echo <<<HTML
                </select>
            HTML;
                    $submit_button = new ButtonComponent("Потвърди", ButtonStyleType::Primary, true);
                    echo $submit_button->render();
                    echo <<<HTML
            </form>
                </div>
            HTML;
                }
            }
        }
        ?>

        <?php echo $message->render(); ?>

    </main>

    <?php echo $footer->render(); ?>
</body>

</html>