var addUserForm = document.getElementById('addUserForm');

if (addUserForm) {
    addUserForm.addEventListener('submit', function (event) {
        var name = addUserForm.name.value.trim();
        var email = addUserForm.email.value.trim();
        var password = addUserForm.password.value;
        var role = addUserForm.role.value;
        var isVerified = addUserForm.is_verified.value;
        var hasError = false;
        var nameError = document.getElementById('addUserNameError');
        var emailError = document.getElementById('addUserEmailError');
        var passwordError = document.getElementById('addUserPasswordError');
        var roleError = document.getElementById('addUserRoleError');
        var verifiedError = document.getElementById('addUserVerifiedError');

        clearError(nameError);
        clearError(emailError);
        clearError(passwordError);
        clearError(roleError);
        clearError(verifiedError);

        if (name.length < 2) {
            hasError = true;
            showError(nameError, 'Name must be at least 2 characters.');
        }

        if (email === '' || email.indexOf('@') === -1 || email.indexOf('.') === -1) {
            hasError = true;
            showError(emailError, 'Enter a valid email address.');
        }

        if (password.length < 8) {
            hasError = true;
            showError(passwordError, 'Password must be at least 8 characters.');
        }

        if (role !== 'admin' && role !== 'scout' && role !== 'user') {
            hasError = true;
            showError(roleError, 'Please select a valid role.');
        }

        if (isVerified !== '0' && isVerified !== '1') {
            hasError = true;
            showError(verifiedError, 'Please select a valid verification status.');
        }

        if (hasError) {
            event.preventDefault();
        }
    });
}

function showError(errorBox, message) {
    if (errorBox) {
        errorBox.textContent = message;
        errorBox.style.color = '#d10000';
    }
}

function clearError(errorBox) {
    if (errorBox) {
        errorBox.textContent = '';
        errorBox.style.color = '#d10000';
    }
}
