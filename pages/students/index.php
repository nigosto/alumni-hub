<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../components/header/header_component.php";
require_once __DIR__ . "/../../components/footer/footer_component.php";
require_once __DIR__ . "/../../components/table/table_component.php";

$header = new HeaderComponent();
$footer = new FooterComponent();
$table = new TableComponent(Student::labels(), $controller->get_students_data());

$stylesheets = array_merge(
  $header->get_stylesheets(),
  $footer->get_stylesheets(),
  $table->get_stylesheets(),
  [$_ENV["BASE_URL"] . "/pages/students/styles.css"]
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();
?>

<body>
  <?php
  echo $header->render();
  ?>
  <main id="site-main">
    <section id="students-section">
      <h3>Списък със студенти</h3>
      <a href="students/export">Експорт</a>
      <?php
      echo $table->render();
      ?>
    </section>
  </main>
  <?php
  echo $footer->render();
  ?>
</body>

</html>