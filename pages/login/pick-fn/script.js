document.addEventListener('DOMContentLoaded', () => {
    const pickForm = document.getElementById('form-pick-fn');
    const addForm = document.getElementById('form-add-fn');

    pickForm.addEventListener('submit', async (e) => {
        e.preventDefault();

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

    addForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const fn = document.getElementById('fn').value;
        const baseUrl = localStorage.getItem("baseUrl");

        try {
            const response = await fetch(`${baseUrl}/add-fn`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ fn }),
            });

            const data = await response.json();

            if (response.ok) {
                // TODO: show message, but don't redirect
                // window.location.href = `${baseUrl}/profile`;
            } else {
                throw new Error(data.message || 'Could not add faculty number');
            }
        } catch (error) {
            showPopup(error.message);
        }
    });
});

