function showPopup(message) {
    popup.textContent = message;

    popup.classList.remove("hidden", "hide");
    popup.classList.add("show");
    setTimeout(() => {
        popup.classList.remove("show");
        popup.classList.add("hide");
    }, 3000);

    popup.addEventListener("animationend", (event) => {
        if (event.animationName === "popupHide") {
            popup.classList.add("hidden");
        }
    });
}