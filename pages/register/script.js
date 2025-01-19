document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registration-form');
    const accountType = document.getElementById('account-type');
    const fn = document.getElementById('fn');

    accountType.addEventListener('change', async (e) => {
        if (e.target.value === 'student') {
            fn.style.display = 'inline';
            fn.required = true;
        } else {
            fn.style.display = 'none';
            fn.required = false;
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const role = document.getElementById('account-type').value;
        const fn = document.getElementById('fn').value;
        const password_confirmation = document.getElementById('password-confirmation').value;

        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, email, role, password, password_confirmation, fn }),
            });

            const data = await response.json();

            if (response.ok) {
                form.reset();
                const baseUrl = localStorage.getItem("baseUrl");
                window.location.href = `${baseUrl}/profile`;
            } else {
                throw new Error(data.message || 'Registration failed');
            }
        } catch (error) {
            console.log(error)
        }
    });
});

