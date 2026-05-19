var loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', function (event) {
        var email = loginForm.email.value.trim();
        var password = loginForm.password.value;
        var hasError = false;
        var emailError = document.getElementById('loginEmailError');
        var passwordError = document.getElementById('loginPasswordError');

        if (emailError) {
            emailError.textContent = '';
        }

        if (passwordError) {
            passwordError.textContent = '';
        }

        if (email === '' || email.indexOf('@') === -1 || email.indexOf('.') === -1) {
            hasError = true;
            if (emailError) {
                emailError.textContent = 'Enter a valid email address.';
                emailError.style.color = '#d10000';
            }
        }

        if (password === '') {
            hasError = true;
            if (passwordError) {
                passwordError.textContent = 'Password is required.';
                passwordError.style.color = '#d10000';
            }
        }

        if (hasError) {
            event.preventDefault();
        }
    });
}
