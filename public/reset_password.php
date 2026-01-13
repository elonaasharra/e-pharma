<?php include_once __DIR__ . '/../includes/no_login/header.php'; ?>

<div class="register-page">
    <div class="register-wrapper" style="max-width:520px; width:100%;">
        <h3 class="mb-4 text-center">Reset Password</h3>

        <form id="resetForm" method="post" novalidate>
            <input type="hidden" id="token" value="<?php echo htmlspecialchars($_GET["token"] ?? ""); ?>">

            <div class="form-group mb-4">
                <input type="password" class="form-control" placeholder="New Password"
                       id="password">
                <span id="password_message" class="pull-left text-danger"></span>
            </div>

            <div class="form-group mb-4">
                <input type="password" class="form-control" placeholder="Confirm Password"
                       id="confirm_password">
                <span id="confirm_password_message" class="pull-left text-danger"></span>
            </div>

            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/no_login/footer.php'; ?>
<script src="/e-pharma/public/assets/js/reset_password.js"></script>
