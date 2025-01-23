document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-fn');

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
                form.reset();
                const baseUrl = localStorage.getItem("baseUrl");
                localStorage.setItem("student_fn", fn);
                window.location.href = `${baseUrl}/profile`;
            } else {
                throw new Error(data.message || 'Could not pick faculty number');
            }
        } catch (error) {
            console.log(error)
        }
    });
});

