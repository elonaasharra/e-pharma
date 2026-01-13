<?php include_once __DIR__ . '/../includes/no_login/header.php'; ?>

<div class="register-page">
    <div class="register-wrapper" style="max-width:520px; width:100%;">

        <h3 class="mb-4 text-center">Login</h3>

        <form id="loginForm" novalidate>

            <div class="form-group mb-4">
                <input type="email" class="form-control" placeholder="Email"
                       name="email" id="login_email">
                <span id="login_email_message" class="pull-left text-danger"></span>
            </div>

            <div class="form-group mb-4">
                <input type="password" class="form-control" placeholder="Password"
                       name="password" id="login_password">
                <span id="login_password_message" class="pull-left text-danger"></span>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="remember_me">
                <label class="form-check-label" for="remember_me">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>

            <div class="text-center mt-3">
                <a href="/e-pharma/public/forgot_password.php">Forgot Password?</a>
            </div>

        </form>

    </div>
</div>

<?php include_once __DIR__ . '/../includes/no_login/footer.php'; ?>
