function validEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validateRegister() {
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const role = document.getElementById("role").value;

    if (name === "" || email === "" || password === "" || confirmPassword === "" || role === "") {
        alert("All fields are required");
        return false;
    }

    if (!validEmail(email)) {
        alert("Enter a valid email address");
        return false;
    }

    if (password.length < 8) {
        alert("Password must be at least 8 characters");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return false;
    }

    return true;
}

function validateLogin() {
    const email = document.getElementById("login_email").value.trim();
    const password = document.getElementById("login_password").value;

    if (email === "" || password === "") {
        alert("Email and password are required");
        return false;
    }

    if (!validEmail(email)) {
        alert("Enter a valid email address");
        return false;
    }

    return true;
}

function validateProfile() {
    const name = document.getElementById("profile_name").value.trim();
    const email = document.getElementById("profile_email").value.trim();
    const currentPassword = document.getElementById("current_password").value;
    const newPassword = document.getElementById("new_password").value;
    const confirmPassword = document.getElementById("profile_confirm_password").value;

    if (name === "" || email === "") {
        alert("Name and email are required");
        return false;
    }

    if (!validEmail(email)) {
        alert("Enter a valid email address");
        return false;
    }

    if ((currentPassword !== "" || newPassword !== "" || confirmPassword !== "") &&
        (currentPassword === "" || newPassword === "" || confirmPassword === "")) {
        alert("Fill all password fields to change password");
        return false;
    }

    if (newPassword !== "" && newPassword.length < 8) {
        alert("New password must be at least 8 characters");
        return false;
    }

    if (newPassword !== confirmPassword) {
        alert("New passwords do not match");
        return false;
    }

    return true;
}

function addToWishlist(postId) {
    fetch("../Controller/WishlistController.php?action=add", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "post_id=" + encodeURIComponent(postId)
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(() => {
            alert("Unable to add item to wishlist.");
        });
}

function removeItem(button, postId) {
    fetch("../Controller/WishlistController.php?action=remove", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "post_id=" + encodeURIComponent(postId)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.closest(".card").remove();
            }
            alert(data.message);
        })
        .catch(() => {
            alert("Unable to remove item from wishlist.");
        });
}
