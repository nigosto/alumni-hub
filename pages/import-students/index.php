<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../components/button/index.php";
require_once __DIR__ . "/../../components/message/message_component.php";

$header = new HeaderComponent();
$footer = new FooterComponent();
$submit_button = new ButtonComponent("Изпращане", ButtonStyleType::Primary, true);
$message = new MessageComponent();

$stylesheets = array_merge(
  $header->get_stylesheets(),
  $footer->get_stylesheets(),
  MessageComponent::get_stylesheets(),
  $submit_button->get_stylesheets(),
  [$_ENV["BASE_URL"] . "/pages/import-students/styles.css"]
);

$meta = new MetadataComponent(
  $stylesheets,
  array_merge(
    MessageComponent::get_scripts(),
    [$_ENV["BASE_URL"] . "/pages/import-students/script.js"]
  )
);
echo $meta->render();
?>

<body>
  <?php
  echo $header->render();
  ?>
  <main id="site-main">
    <section id="import-section">
      <h3>Импортиране на студенти</h3>
      <p>Можете да добавите студенти, директно като качите CSV файл, съдържащ информация за тях. Ако имате таблица на
        Excel с данните за студентите, може да я запазите в CSV формат и да качите съответния файл.</p>
      <form method="POST" action="" id="file-form">
        <label name="import-file" id="import-file-label">
          <input type="file" name="import-file" id="import-file" accept=".csv" />
          <span id="file-name">Няма избран файл</span>
        </label>
        <?php echo $submit_button->render(); ?>
      </form>
    </section>
    <?php echo $message->render(); ?>
  </main>
  <?php
  echo $footer->render();
  ?>
</body>

</html>