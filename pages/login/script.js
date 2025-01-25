document.addEventListener('DOMContentLoaded', () => {
    const message = localStorage.getItem("message");
    if (message) {
        const popup = document.getElementById("success-popup");
        showPopup(popup, message, 6000);
        localStorage.removeItem("message");
    }

    const form = document.getElementById('login-form');

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
            const popup = document.getElementById("error-popup");
            showPopup(popup, error.message);
        }
    });
});

