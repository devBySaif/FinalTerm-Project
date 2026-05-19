<!DOCTYPE html>
<html>
<head>

    <title>Login</title>

    <link rel="stylesheet" href="../Public/CSS/Scout_login_style.css">

</head>

<body>

<div class="login-container">

    <h2>Scout Login</h2>

   <?php
if (!empty($errorMessage)) {
    echo "<p class='error-message'>" .
        htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') .
        "</p>";
}
?>
    <form action="../Controller/Scout_login_controller.php" method="POST">

        <label>Email</label>

        <input type="email"
        name="email"
        placeholder="Enter Email">

        <label>Password</label>

        <input type="password"
        name="password"
        placeholder="Enter Password">

        <div class="remember">

            <input type="checkbox" name="remember">
            Remember Me

        </div>

        <input type="submit" name="login" value="Login">

    </form>

</div>

</body>
</html>
