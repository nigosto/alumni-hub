document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('pick-fn');

    select.addEventListener('change', async (e) => {
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
            } else {
                throw new Error(data.message || 'Could not pick faculty number');
            }
        } catch (error) {
            console.log(error)
        }
    });
});

