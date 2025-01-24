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
            <div class="form-entry-container">
                <label class="form-label">Дата на церемонията: </label>
                <input type="datetime-local" id="date" name="date" required>
            </div>

            <div class="form-entry-container">
                <label class="form-label">Година на завършване: </label>
                <input type="number" id="graduation-year" name="graduation-year" placeholder="Година" required>
            </div>

            <div class="form-entry-container">
                <label class="form-label">Студент, изнасящ церемониална реч: </label>
                <input type="text" id="speaker" name="speaker" placeholder="Факултетен номер" required>
            </div>

            <div class="form-entry-container">
                <label class="form-label">Студент, отговорник за церемониалните тоги: </label>
                <input type="text" id="responsible-robes" name="responsible-robes" 
                    placeholder="Факултетен номер" required>
            </div>

            <div class="form-entry-container">
                <label class="form-label">Студент, отговорник за дипломните подписи: </label>
                <input type="text" id="responsible-signatures" name="responsible-signatures"
                    placeholder="Факултетен номер" required>
            </div>
                
            <div class="form-entry-container">
                <label class="form-label">Студент, отговорник по връчване на дипломите: </label>
                <input type="text" id="responsible-diplomas" name="responsible-diplomas"
                    placeholder="Факултетен номер" required>
            </div>

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