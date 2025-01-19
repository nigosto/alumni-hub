document.addEventListener('DOMContentLoaded', () => {
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
                localStorage.setItem("role", user.role);

                if (user.role === "student") {
                    window.location.href = `${baseUrl}/login/pick-fn`;
                }
                else {
                    const baseUrl = localStorage.getItem("baseUrl");
                    window.location.href = `${baseUrl}/profile`;
                }
            } else {
                throw new Error(data.message || 'Login failed');
            }
        } catch (error) {
            console.log(error)
        }
    });
});

