window.addEventListener("load", () => {
  const form = document.getElementById("file-form");
  const file = document.getElementById("import-file");

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const reader = new FileReader();
    reader.onload = async function(e) {
      await fetch('../api/import_students.php', {
        method: 'POST',
        body: JSON.stringify({
          file: e.target.result.split("base64,")[1]
        })
      })
    };
    reader.readAsDataURL(file.files[0]);
  });
});