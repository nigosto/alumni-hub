document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-fn');
    const popup = document.getElementById("popup");

    form.addEventListener('submit', async (e) => {
        const fn = document.getElementById('pick-fn').value;
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ fn }),
            });

            const data = await response.json();

            if (response.ok) {
                const baseUrl = localStorage.getItem("baseUrl");
                window.location.href = `${baseUrl}/profile`;
            } else {
                throw await response.json();
            }
        } catch (error) {
            showPopup(error.message);
        }
    });

    function showPopup(message) {
        popup.textContent = message;
        popup.style.display = "block";

        setTimeout(() => {
            popup.style.display = "none";
        }, 3000);
    }
});

