async function buttonHandler(url, ceremony_id, status) {
    try {
        const response = await fetch(`${localStorage.getItem("baseUrl")}/${url}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ceremony_id, status }),
        });

        const data = await response.json();

        if (response.ok) {
            window.location.reload();
        } else {
            throw new Error(data.message || 'Could not clothes size');
        }
    } catch (error) {
        console.log(error)
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('clothes');

    form?.addEventListener('submit', async (e) => {
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
            const popup = document.getElementById("error-popup");
            showPopup(popup, error.message);
        }
    });

    const accept_speach_buttons = document.getElementsByClassName('accept-speach-btn');
    Array.from(accept_speach_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/speach", ceremony_id, 'accepted');
        });
    });
    
    const decline_speach_buttons = document.getElementsByClassName('decline-speach-btn');
    Array.from(decline_speach_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/speach", ceremony_id, 'declined');           
        });
    })

    const accept_invitation_buttons = document.getElementsByClassName('accept-invitation-btn');
    Array.from(accept_invitation_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance", ceremony_id, true);           
        });
    });

    const decline_invitation_buttons = document.getElementsByClassName('decline-invitation-btn');
    Array.from(decline_invitation_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance", ceremony_id, false);           
        });
    });

    const accept_diplomas_buttons = document.getElementsByClassName('accept-diplomas-btn');
    Array.from(accept_diplomas_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/responsibility", ceremony_id, "accepted_diplomas");           
        });
    });

    const decline_diplomas_buttons = document.getElementsByClassName('decline-diplomas-btn');
    Array.from(decline_diplomas_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/responsibility", ceremony_id, "declined_diplomas");           
        });
    });

    const accept_robes_buttons = document.getElementsByClassName('accept-robes-btn');
    Array.from(accept_robes_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/responsibility", ceremony_id, "accepted_robes");           
        });
    });

    const decline_robes_buttons = document.getElementsByClassName('decline-robes-btn');
    Array.from(decline_robes_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/responsibility", ceremony_id, "declined_robes");           
        });
    });

    const accept_signatures_buttons = document.getElementsByClassName('accept-signatures-btn');
    Array.from(accept_signatures_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/responsibility", ceremony_id, "accepted_signatures");           
        });
    });

    const decline_signatures_buttons = document.getElementsByClassName('decline-signatures-btn');
    Array.from(decline_signatures_buttons).forEach(button => {
        const ceremony_id = button.getAttribute('data-param');
        button.addEventListener('click', async (e) => {
            buttonHandler("ceremonies/attendance/responsibility", ceremony_id, "declined_signatures");           
        });
    });
});

