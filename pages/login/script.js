document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    const popup = document.getElementById("popup");

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }),
            });
            const baseUrl = localStorage.getItem("baseUrl");

            if (response.ok) {
                form.reset();
                const user = await response.json();
                localStorage.setItem("role", user.role);

                if (user.role.toLowerCase() === "student") {
                    window.location.href = `${baseUrl}/login/pick-fn`;
                }
                else {
                    const baseUrl = localStorage.getItem("baseUrl");
                    window.location.href = `${baseUrl}/profile`;
                }
            }
            else {
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

