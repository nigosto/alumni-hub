window.addEventListener("load", () => {
  const form = document.getElementById("file-form");
  const file = document.getElementById("import-file");
  const filename = document.getElementById("file-name");
  const popup = document.getElementById("error-popup");

  file.addEventListener('change', (event) => {
    try {
      const currentFile = event.target.files[0];
      const currentFilename = currentFile?.name ?? "Няма избран файл";
      
      if (currentFile.type === "text/csv") {
        filename.textContent = currentFilename;
      } else {
        file.value = "";
        throw new Error("Невалиден CSV файл");
      }
    } catch (error) {
      showPopup(popup, error.message);
    }
  });

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const baseUrl = localStorage.getItem("baseUrl");

    const reader = new FileReader();
    try {
      reader.onload = async function (e) {
        fetch('import', {
          method: 'POST',
          body: JSON.stringify({
            file: e.target.result.split("base64,")[1]
          })
        }).then(() => window.location.href = `${baseUrl}/students`);
      };
      reader.readAsDataURL(file.files[0]);
    }
    catch (error) {
      showPopup(popup, error.message);
    }
  });
});