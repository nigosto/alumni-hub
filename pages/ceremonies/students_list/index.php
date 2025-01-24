<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../../components/metadata/metadata_component.php";
require_once __DIR__ . "/../../../components/header/header_component.php";
require_once __DIR__ . "/../../../components/footer/footer_component.php";
require_once __DIR__ . "/../../../components/table/table_component.php";
require_once __DIR__ . "/../../../components/button/link.php";

$header = new HeaderComponent();
$footer = new FooterComponent();
$students_table = new TableComponent(CeremonyAttendance::labels_ceremony_students_list(), 
    $ceremonies_controller->get_ceremony_students_info($ceremony_id));
$export_link = new LinkComponent("Експорт", "{$_ENV["BASE_URL"]}/ceremony/students/{$ceremony_id}/export");
$edit_link = new LinkComponent("Редактиране", "{$_ENV["BASE_URL"]}/ceremony/edit/{$ceremony_id}");

$stylesheets = array_merge(
  $header->get_stylesheets(),
  $footer->get_stylesheets(),
  $students_table->get_stylesheets(),
  $export_link->get_stylesheets(),
  $edit_link->get_stylesheets(),
  [$_ENV["BASE_URL"] . "/pages/ceremonies/students_list/styles.css"],
);

$meta = new MetadataComponent($stylesheets);
echo $meta->render();

$ceremony_info = $ceremonies_controller->get_ceremony_simple_info_by_id($ceremony_id)->to_array();
?>

<body>
  <?php
  echo $header->render();
  ?>
  <main id="site-main">
    <section id="students-section">
      <h3>Списък със студенти за церемония</h3>
      <div id="ceremony-info-container">
          <?php
          echo <<<HTML
          <p class="entry"><strong class="entry-name">Дата на церемонията:</strong> {$ceremony_info["date"]}</p>
          <p class="entry"><strong class="entry-name">Година на завършване:</strong> {$ceremony_info["graduation_year"]}</p>
          HTML;
          ?>
      </div>

      <div id="buttons-container">
      <?php 
      echo $edit_link->render();
      echo $export_link->render();
      ?>
      </div>
      <?php  
      echo $students_table->render();
      ?>
    </section>
  </main>
  <?php
  echo $footer->render();
  ?>
</body>

</html>