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

            if (response.ok) {
                const baseUrl = localStorage.getItem("baseUrl");

                if (role === "student") {
                    localStorage.setItem("message", "Заявката е изпратена успешно! Моля изчакайте докато бъдете одобрени!");
                    window.location.href = `${baseUrl}/login`;
                } else {
                    window.location.href = `${baseUrl}/profile`;
                }
            } else {
                throw await response.json();
            }
        } catch (error) {
            const popup = document.getElementById("error-popup");
            showPopup(popup, error.message);
        }
    });
});

