$(document).ready(function () {
    $("#resetForm").on("submit", function (e) {
        e.preventDefault();

        let token = $("#token").val().trim();
        let password = $("#password").val().trim();
        let confirm = $("#confirm_password").val().trim();
        let error = 0;

        $(".text-danger").text("");
        $(".form-control").removeClass("border-danger");

        if (password.length < 6) {
            $("#password").addClass("border-danger");
            $("#password_message").text("Password must be at least 6 characters.");
            error++;
        }

        if (confirm !== password) {
            $("#confirm_password").addClass("border-danger");
            $("#confirm_password_message").text("Passwords do not match.");
            error++;
        }

        if (!token) {
            alert("Invalid reset token.");
            return;
        }

        if (error > 0) return;

        let data = new FormData();
        data.append("action", "reset_password");
        data.append("token", token);
        data.append("password", password);

        $.ajax({
            type: "POST",
            url: "ajax/ajax_reset_password.php",
            dataType: "json",
            processData: false,
            contentType: false,
            data: data,
            success: function (response) {
                alert(response.message);
                if (response.location) {
                    window.location.href = response.location;
                }
            },
            error: function () {
                alert("AJAX error");
            }
        });
    });
});
