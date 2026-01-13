$(document).ready(function () {

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        let email = $("#login_email").val().trim();
        let password = $("#login_password").val();
        let remember = $("#remember_me").is(":checked") ? 1 : 0;

        let email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let error = 0;

        // reset
        $("#login_email, #login_password").removeClass("border-danger");
        $("#login_email_message, #login_password_message").text("");

        // validime front end

        if (!email_regex.test(email)) {
            $("#login_email").addClass("border-danger");
            $("#login_email_message").text("Invalid email format");
            error++;
        }

        if (!password || password.length < 1) {
            $("#login_password").addClass("border-danger");
            $("#login_password_message").text("Password can not be empty");
            error++;
        }

        if (error > 0) return;

        let data = new FormData();
        data.append("action", "login");
        data.append("email", email);
        data.append("password", password);
        data.append("remember_me", remember);

        $.ajax({
            type: "POST",
            url: "ajax/ajax_login.php",
            processData: false,
            contentType: false,
            data: data,
            dataType: "json",
            success: function (response) {
                if (typeof response === "string") response = JSON.parse(response);
                alert(response.message);
                if (response.location) window.location.href = response.location;
            },
            error: function () {
                alert("AJAX error");
            }
        });
    });

});
