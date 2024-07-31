document.getElementById('upload-form').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const response = await fetch('/upload', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    document.getElementById('resultContent').innerHTML = `<p>${result.message}</p><p>Prediction: ${result.prediction}</p>`;
    document.getElementById('resultModal').style.display = 'block';
});

document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('resultModal').style.display = 'none';
});

window.addEventListener('click', function(event) {
    const modal = document.getElementById('resultModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
});

document.getElementById('upload-form').addEventListener('submit', async function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    const response = await fetch('/upload', {
        method: 'POST',
        body: formData
    });
    const result = await response.json();
    document.getElementById('resultContent').innerHTML = `<p>${result.message}</p><p>Prediction: ${result.prediction}</p>`;
});

document.getElementById('search-input').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        const query = this.value.toLowerCase();
        const sections = document.querySelectorAll('section');
        let found = false;

        sections.forEach(section => {
            const textContent = section.textContent.toLowerCase();
            if (textContent.includes(query)) {
                section.style.display = 'block';
                found = true;
            } else {
                section.style.display = 'none';
            }
        });

        if (!found) {
            alert('No matching content found');
        }
    }
});



function loadDoctors() {
    const doctors = JSON.parse(localStorage.getItem('doctors')) || [];
    const container = document.getElementById('doctorBoxContainer');
    container.innerHTML = '';
    doctors.forEach(doctor => {
        const box = document.createElement('div');
        box.classList.add('box');
        box.innerHTML = `
            <img src="static/image/doc-1.jpg" alt="Doctor Image">
            <h3>${doctor.name}</h3>
            <span>${doctor.specialty}</span>
            <div class="share">
                <a href="mailto:example@example.com" class="fas fa-envelope"></a>
                <a href="tel:+1234567890" class="fas fa-phone"></a>
            </div>
            <div class="appointment">
                <a href="http://localhost/appointment_system/appointment_form.php" class="btn">Book Appointment</a>
            </div>
        `;
        container.appendChild(box);
    });
}

window.onload = loadDoctors;