// document.addEventListener('DOMContentLoaded', () => {
//     const form = document.getElementById('creation-form');

//     form.addEventListener('submit', async (e) => {
//         e.preventDefault();

//         const date = document.getElementById('date').value;
//         const graduation_year = document.getElementById('graduation-year').value;
//         const speaker = document.getElementById('speaker').value;
//         const responsible_robes = document.getElementById('responsible-robes').value;
//         const responsible_signatures = document.getElementById('responsible-signatures').value;
//         const responsible_diplomas = document.getElementById('responsible-diplomas').value;

//         try {
//             const response = await fetch('', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json',
//                 },
//                 body: JSON.stringify({ date, graduation_year, speaker, responsible_robes, responsible_signatures, responsible_diplomas }),
//             });

//             const data = await response.json();

//             if (response.ok) {
//                 form.reset();
//             } else {
//                 // TODO: proper error handling
//                 throw new Error(data.message || 'Ceremony creation failed');
//             }
//         } catch (error) {
//             console.log(error)
//         }
//     });
// });

