const requestForm = document.getElementById('requestForm');
const formErrors = document.getElementById('formErrors');

function showErrors(errors) {
    if (!formErrors) {
        return;
    }

    formErrors.innerHTML = errors.map((error) => `<div>${error}</div>`).join('');
    formErrors.classList.remove('hidden');
}

if (requestForm) {
    requestForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const errors = [];
        const image = document.getElementById('imageInput');

        ['title', 'short_history', 'country', 'country_representation', 'genre', 'cost_level', 'travel_medium_info'].forEach((name) => {
            const field = requestForm.elements[name];
            if (!field || field.value.trim() === '') {
                errors.push(`${name.replaceAll('_', ' ')} is required.`);
            }
        });

        if (image && image.files.length > 0) {
            const file = image.files[0];
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowed.includes(file.type)) {
                errors.push('Only JPG, PNG, or WEBP images are allowed.');
            }
            if (file.size > 2 * 1024 * 1024) {
                errors.push('Image must be 2MB or smaller.');
            }
        }

        if (errors.length > 0) {
            showErrors(errors);
            return;
        }

        fetch(requestForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(requestForm)
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    window.location.href = 'scout_requests.php';
                    return;
                }

                showErrors(data.errors || [data.message || 'Save failed.']);
            })
            .catch(() => showErrors(['Save failed.']));
    });
}

document.querySelectorAll('.delete-request').forEach((button) => {
    button.addEventListener('click', function () {
        if (!confirm('Delete this pending request?')) {
            return;
        }

        const formData = new FormData();
        formData.append('request_id', this.dataset.id);
        formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('../Controller/ScoutCrudController.php?action=delete', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById(`request-${this.dataset.id}`).remove();
                    return;
                }

                alert(data.message || 'Delete failed.');
            })
            .catch(() => alert('Delete failed.'));
    });
});
