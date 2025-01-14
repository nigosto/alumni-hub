<!DOCTYPE html>
<html lang="bg">

<?php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../components/meta/index.php";
require_once __DIR__ . "/../../components/header/index.php";
require_once __DIR__ . "/../../components/footer/index.php";

$header = new HeaderComponent();
$footer = new FooterComponent();

$stylesheets = array_merge(
  $header->get_stylesheets(),
  $footer->get_stylesheets()
);

$meta = new MetadataComponent($stylesheets, [$_ENV["BASE_URL"] . "/pages/import-students/script.js"]);
echo $meta->render();
?>

<body>
  <?php
  echo $header->render();
  ?>
  <main id="site-main">
    <form method="POST" action="" id="file-form">
      <input type="file" name="import-file" id="import-file" />
      <input type="submit" value="Изпрати">
    </form>
  </main>
  <?php
  echo $footer->render();
  ?>
</body>

</html>