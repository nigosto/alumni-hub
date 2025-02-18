<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../../components/header/header_component.php";
require_once __DIR__ . "/../../../components/footer/footer_component.php";
require_once __DIR__ . "/../../../components/button/link.php";
require_once __DIR__ . "/../../../components/message/message_component.php";

$header = new HeaderComponent();
$footer = new FooterComponent();
$message = new MessageComponent(MessageVariant::Error);
$info_message = new MessageComponent(MessageVariant::Success);

$base_url = $_ENV["BASE_URL"];

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/login/pick-fn/styles.css"],
    [$base_url . "/components/styles/input.css"],
    ButtonComponent::get_stylesheets(),
    MessageComponent::get_stylesheets()
);

session_start();
$user_id = $_SESSION["id"];
$students = $controller->students_service->get_students_by_user_id($user_id);

$meta = new MetadataComponent($stylesheets, array_merge(
    MessageComponent::get_scripts(),
    ["$base_url/pages/login/pick-fn/script.js"]
));
echo $meta->render();
?>

<body>
    <?php
    echo $header->render();
    ?>

    <main class="container">
        <h3>Моля изберете факултетния номер, с който да влезете</h3>
        <form id="form-pick-fn">
            <select name="pick-fn" id="pick-fn">
                <option value="" disabled selected>Избор на факултетен номер</option>
                <?php foreach ($students as $student) {
                    $fn = $student->to_array()["fn"];
                    echo <<<HTML
                        <option value="{$fn}"> $fn
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

        <h3>Или добавете нов факултетен номер</h3>

        <form id="form-add-fn">
            <input type="text" id="fn" name="fn" placeholder="Факултетен номер">

            <?php
            $submit_button = new ButtonComponent("Добави", ButtonStyleType::Primary, true);
            echo $submit_button->render();
            ?>

        </form>

        <?php echo $message->render(); ?>
        <?php echo $info_message->render(); ?>

    </main>

    <?php echo $footer->render();
    ?>
</body>

</html>