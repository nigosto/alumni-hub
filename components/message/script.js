function showPopup(popup, message, timeout = 3000) {
    popup.textContent = message;

    popup.classList.remove("hidden", "hide");
    popup.classList.add("show");
    setTimeout(() => {
        popup.classList.remove("show");
        popup.classList.add("hide");
    }, timeout);

    popup.addEventListener("animationend", (event) => {
        if (event.animationName === "popupHide") {
            popup.classList.add("hidden");
        }
    });
}