<?php include_once __DIR__ . '/../includes/no_login/header.php'; ?>

<div class="register-page">
    <div class="register-wrapper" style="max-width:520px; width:100%;">
        <h3 class="mb-4 text-center">Forgot Password</h3>

        <form id="forgotForm" method="post" novalidate>
            <div class="form-group mb-4">
                <input type="email" class="form-control" placeholder="Email"
                       name="email" id="fp_email">
                <span id="fp_email_message" class="pull-left text-danger"></span>
            </div>

            <button type="submit" class="btn btn-primary w-100">Send reset link</button>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/no_login/footer.php'; ?>
<script src="/e-pharma/public/assets/js/forgot_password.js"></script>
