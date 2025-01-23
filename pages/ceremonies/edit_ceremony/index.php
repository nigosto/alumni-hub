<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../../components/header/header_component.php";
require_once __DIR__ . "/../../../components/footer/footer_component.php";
require_once __DIR__ . "/../../../components/button/link.php";

$base_url = $_ENV["BASE_URL"];
$header = new HeaderComponent();
$footer = new FooterComponent();

$stylesheets = array_merge(
    $header->get_stylesheets(),
    $footer->get_stylesheets(),
    [$base_url . "/pages/ceremonies/edit_ceremony/styles.css"],
    [$base_url . "/components/styles/input.css"],
    [$base_url . "/components/styles/form.css"],
    ButtonComponent::get_stylesheets()
);

$meta = new MetadataComponent($stylesheets, ["$base_url/pages/ceremonies/edit_ceremony/script.js"]);
echo $meta->render();

$ceremony_info = $ceremonies_controller->get_ceremony_by_id($ceremony_id);
// Change date so that it is in a format that is suitable for JS
$ceremony_info["date"] = str_replace(' ', 'T', $ceremony_info["date"]);
?>

<body>
    <?php echo $header->render(); ?>

    <main class="container">
        <h1>Редактиране на церемония</h1>

        <form id="edit-form">
            <input type="datetime-local" id="date" name="date" placeholder="Дата на церемонията" required
            required value="<?= htmlspecialchars($ceremony_info['date'], ENT_QUOTES, 'UTF-8')?>">
            <input type="number" id="graduation-year" name="graduation-year" placeholder="Година на завършване" required
            value="<?= $ceremony_info['graduation_year']?>">
            <input type="text" id="speaker" name="speaker" placeholder="Покана към студент за церемониална реч" required 
            value="<?= htmlspecialchars($ceremony_info['speaker'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" id="responsible-robes" name="responsible-robes" 
                placeholder="Покана към студент за отговорник на церемониалните тоги" required
                required value="<?= htmlspecialchars($ceremony_info['responsible_robes'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" id="responsible-signatures" name="responsible-signatures"
                placeholder="Покана към студент за отговорник на дипломните подписи" required
                required value="<?= htmlspecialchars($ceremony_info['responsible_signatures'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" id="responsible-diplomas" name="responsible-diplomas"
                placeholder="Покана към студент за отговорник по връчване на дипломите" required
                required value="<?= htmlspecialchars($ceremony_info['responsible_diplomas'], ENT_QUOTES, 'UTF-8') ?>">

            <?php
                $submit_button = new ButtonComponent("Редактиране на церемония", ButtonStyleType::Primary, true);
                echo $submit_button->render();
            ?>
        </form>
    </main>

    <?php echo $footer->render(); ?>
</body>

</html>