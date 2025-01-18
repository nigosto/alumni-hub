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

            if (response.ok) {
                form.reset();
                const user = await response.json();
                localStorage.setItem("role", user.role);

                if (user.role === "Student") {
                    window.location.href = "login/pick-fn";
                }
                else { window.location.href = "#"; }
            } else {
                throw new Error(data.message || 'Login failed');
            }
        } catch (error) {
            console.log(error)
        }
    });
});

