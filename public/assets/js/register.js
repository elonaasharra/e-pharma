console.log("register.js loaded");

$(document).ready(function () {

    function showRegisterAlert(msg, type) {
        $("#registerAlert")
            .removeClass("d-none alert-success alert-danger alert-warning alert-info")
            .addClass("alert-" + type)
            .text(msg);
    }

    function isEmpty(val) {
        return !val || val.trim() === "";
    }

    $("#registerForm").on("submit", function (e) {
        e.preventDefault();

        let name = $("#name").val();
        let surname = $("#surname").val();
        let email = $("#email").val();
        let password = $("#password").val();
        let confirm_password = $("#confirm_password").val();

        let email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let alpha_regex = /^[a-zA-Z]{3,40}$/;
        let error = 0;

        // reset UI
        $("#registerAlert").addClass("d-none").text("").removeClass("alert-success alert-danger alert-warning alert-info");
        $(".form-control").removeClass("border-danger");
        $("span.text-danger").text("");

        // VALIDIME FRONTEND
        if (!alpha_regex.test(name)) {
            $("#name").addClass("border-danger");
            $("#name_message").text("Name must contain only letters (min 3).");
            error++;
        }

        if (!alpha_regex.test(surname)) {
            $("#surname").addClass("border-danger");
            $("#surname_message").text("Surname must contain only letters (min 3).");
            error++;
        }

        if (!email_regex.test(email)) {
            $("#email").addClass("border-danger");
            $("#email_message").text("Invalid email format.");
            error++;
        }

        if (isEmpty(password) || password.length < 8) {
            $("#password").addClass("border-danger");
            $("#password_message").text("Password must be at least 8 characters.");
            error++;
        }

        if (confirm_password !== password) {
            $("#confirm_password").addClass("border-danger");
            $("#confirm_password_message").text("Passwords do not match.");
            error++;
        }

        if (error > 0) return;

        let data = new FormData();
        data.append("action", "register");
        data.append("name", name);
        data.append("surname", surname);
        data.append("email", email);
        data.append("password", password);
        data.append("confirm_password", confirm_password);

        $.ajax({
            type: "POST",
            url: "ajax/ajax_register.php",
            processData: false,
            contentType: false,
            data: data,
            dataType: "json",

            success: function (response) {
                if (typeof response === "string") response = JSON.parse(response);

                showRegisterAlert(
                    response.message || "Registration completed successfully.",
                    response.error ? "danger" : "success"
                );

                // optional: reset form pas suksesit
                if (!response.error) {
                    $("#registerForm")[0].reset();
                }
            },

            error: function (xhr) {
                let msg = "AJAX error";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showRegisterAlert(msg, "danger");
            }
        });

    });

});
