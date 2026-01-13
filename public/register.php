<?php include_once __DIR__ . '/../includes/no_login/header.php'; ?>

<div class="register-page">
    <div class="register-wrapper" style="max-width:520px; width:100%;">

        <h3 class="mb-4 text-center">Create Account</h3>

        <form id="registerForm" novalidate  >

            <div class="form-group mb-4">
                <input type="text" class="form-control" placeholder="Name"
                       name="name" id="name">
                <span id="name_message" class="pull-left text-danger"></span>
            </div>

            <div class="form-group mb-4">
                <input type="text" class="form-control" placeholder="Surname"
                       name="surname" id="surname">
                <span id="surname_message" class="pull-left text-danger"></span>
            </div>

            <div class="form-group mb-4">
                <input type="email" class="form-control" placeholder="Email"
                       name="email" id="email">
                <span id="email_message" class="pull-left text-danger"></span>
            </div>

            <div class="form-group mb-4">
                <input type="password" class="form-control" placeholder="Password"
                       name="password" id="password">
                <span id="password_message" class="pull-left text-danger"></span>
            </div>

            <div class="form-group mb-4">
                <input type="password" class="form-control" placeholder="Confirm Password"
                       name="confirm_password" id="confirm_password">
                <span id="confirm_password_message" class="pull-left text-danger"></span>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Register
            </button>
        </form>

    </div>
</div>

<?php include_once __DIR__ . '/../includes/no_login/footer.php'; ?>
