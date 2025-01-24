<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../../components/header/header_component.php";
require_once __DIR__ . "/../../../components/footer/footer_component.php";
require_once __DIR__ . "/../../../components/button/link.php";
require_once __DIR__ . "/../../components/message/message_component.php";

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();
$message = new MessageComponent();

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/ceremonies/create_ceremony/styles.css"],
    [$base_url . "/components/styles/input.css"],
    [$base_url . "/components/styles/form.css"],
    ButtonComponent::get_stylesheets(),
    MessageComponent::get_stylesheets()
);

$meta = new MetadataComponent($stylesheets, array_merge(
    MessageComponent::get_scripts(),
    ["$base_url/pages/ceremonies/create_ceremony/script.js"]
));
echo $meta->render();
?>

<body>
    <?php echo $header->render(); ?>

    <main class="container">
        <h1>Създаване на церемония</h1>

        <form id="creation-form">
            <input type="datetime-local" id="date" name="date" placeholder="Дата на церемонията" required>
            <input type="number" id="graduation-year" name="graduation-year" placeholder="Година на завършване"
                required>
            <input type="text" id="speaker" name="speaker" placeholder="Покана към студент за церемониална реч"
                required>
            <input type="text" id="responsible-robes" name="responsible-robes"
                placeholder="Покана към студент за отговорник на церелониалните тоги" required>
            <input type="text" id="responsible-signatures" name="responsible-signatures"
                placeholder="Покана към студент за отговорник на дипломните подписи" required>
            <input type="text" id="responsible-diplomas" name="responsible-diplomas"
                placeholder="Покана към студент за отговорник по връчване на дипломите" required>

            <?php
            $submit_button = new ButtonComponent("Създаване на церемония", ButtonStyleType::Primary, true);
            echo $submit_button->render();
            ?>
        </form>

        <?php echo $message->render(); ?>

    </main>

    <?php echo $footer->render(); ?>
</body>

</html>