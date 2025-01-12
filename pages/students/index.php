<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../components/meta/index.php";
require_once __DIR__ . "/../../components/header/index.php";
require_once __DIR__ . "/../../components/footer/index.php";
require_once __DIR__ . "/../../components/table/index.php";
require_once __DIR__ . "/../../services/students/index.php";

$students = array_map(function ($student) {
    $values = array_values($student->to_array(true));
    array_pop($values);
    return $values;
}, $students_service->find_all());

$header = new HeaderComponent();
$footer = new FooterComponent();
$table = new TableComponent(Student::labels(), $students);

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