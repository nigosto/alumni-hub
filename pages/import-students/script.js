window.addEventListener("load", () => {
  const form = document.getElementById("file-form");
  const file = document.getElementById("import-file");
  const filename = document.getElementById("file-name");

  file.addEventListener('change', (event) => {
    const currentFile = event.target.files[0];
    const currentFilename = currentFile?.name ?? "Няма избран файл";
    
    if (currentFile.type === "text/csv") {
      filename.textContent = currentFilename;
    } else {
      file.value = "";
    }
  });

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    const reader = new FileReader();
    try {
      reader.onload = async function (e) {
        await fetch('import', {
          method: 'POST',
          body: JSON.stringify({
            file: e.target.result.split("base64,")[1]
          })
        });
      };
      reader.readAsDataURL(file.files[0]);
    }
    catch (error) {
      showPopup(error.message);
    }
  });
});