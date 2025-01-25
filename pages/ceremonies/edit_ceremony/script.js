document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('edit-form');

    const baseUrl = localStorage.getItem("baseUrl");

    const input_date = document.getElementById('date');
    const input_graduation_year = document.getElementById('graduation-year');
    const input_speaker = document.getElementById('speaker');
    const input_responsible_robes = document.getElementById('responsible-robes');
    const input_responsible_signatures = document.getElementById('responsible-signatures');
    const input_responsible_diplomas = document.getElementById('responsible-diplomas');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        date = input_date.value;
        graduation_year = input_graduation_year.value;
        // 1MI1234567 (Unconfirmed) => 1MI1234567
        speaker = input_speaker.value.split(" ")[0];
        responsible_robes = input_responsible_robes.value.split(" ")[0];
        responsible_signatures = input_responsible_signatures.value.split(" ")[0];
        responsible_diplomas = input_responsible_diplomas.value.split(" ")[0];

        try {
            response = await fetch('', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ date, graduation_year, speaker, responsible_robes, responsible_signatures, responsible_diplomas }),
            });

            if (response.ok) {
                window.location.href = `${baseUrl}/ceremonies`;
            } else {
                throw await response.json();
            }
        } catch (error) {
            const popup = document.getElementById("error-popup");
            showPopup(popup, error.message);
        }
    });
});

