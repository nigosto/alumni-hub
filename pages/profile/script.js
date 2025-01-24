document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('clothes');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const size = document.getElementById('pick-size').value;
        try {
            const response = await fetch(`${localStorage.getItem("baseUrl")}/clothes`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ size }),
            });

            const data = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                throw await response.json();
            }
        } catch (error) {
            showPopup(error.message);
        }
    });
});