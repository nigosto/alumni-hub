<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../../components/header/header_component.php";
require_once __DIR__ . "/../../../components/footer/footer_component.php";
require_once __DIR__ . "/../../../components/button/link.php";

$header = new HeaderComponent();
$footer = new FooterComponent();

$base_url = $_ENV["BASE_URL"];

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/login/pick-fn/styles.css"],
    [$base_url . "/components/styles/input.css"],
    ButtonComponent::get_stylesheets()
);

session_start();
$user_id = $_SESSION["id"];
$students = $controller->students_service->get_students_by_user_id($user_id);

$meta = new MetadataComponent($stylesheets, ["$base_url/pages/login/pick-fn/script.js"]);
echo $meta->render();
?>

<body>
    <?php
    echo $header->render();
    ?>

    <main class="container">
        <h1>Моля изберете факултетния номер, с който да влезете</h1>
        <form id="form-fn">
            <select name="pick-fn" id="pick-fn">
                <option value="" disabled selected>Избор на факултетен номер</option>
                <?php foreach ($students as $student) {
                    echo <<<HTML
                        <option value="{$student->get_fn()}">{$student->get_fn()}
                    </option>
                    HTML;
                }
                ?>
            </select>

            <?php
            $submit_button = new ButtonComponent("Избери", ButtonStyleType::Primary, true);
            echo $submit_button->render();
            ?>

        </form>
    </main>

    <?php echo $footer->render();
    ?>
</body>

</html>