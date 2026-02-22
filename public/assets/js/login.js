$(document).ready(function () {
    function showLoginAlert(msg, type) {
        // type: "success" | "danger" | "warning" | "info"
        $("#loginAlert")
            .removeClass("d-none alert-success alert-danger alert-warning alert-info")
            .addClass("alert-" + type)
            .text(msg);
    }
    //ne submit te formes ekzekutohet ky funksion
    $("#loginForm").on("submit", function (e) {
        e.preventDefault();
        let email = $("#login_email").val().trim();
        let password = $("#login_password").val();
        let remember = $("#remember_me").is(":checked") ? 1 : 0;
        let email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let error = 0;

        //shtesa
        $("#loginAlert").addClass("d-none").text("").removeClass("alert-success alert-danger alert-warning alert-info");

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

        let data = new FormData();//pergatisim te dhenat per dergim me objektin form data
        data.append("action", "login");
        data.append("email", email);
        data.append("password", password);
        data.append("remember_me", remember);

        $.ajax({
            type: "POST",
            url: "ajax/ajax_login.php",
            processData: false,//qe tmos i kthejm te dhenat ne querystring
            contentType: false,//ja vendos browseri automatikisht
            data: data,
            dataType: "json",//Është një format për shkëmbim të dhënash midis browserit dhe serverit
            success: function (response) {
                if (typeof response === "string") response = JSON.parse(response);
                showLoginAlert(response.message || "Login success", "success");
                if (response.location) {
                    setTimeout(function () {
                        window.location.href = response.location;
                    }, 800);
                }
            },
            error: function (xhr) {
                let msg = "AJAX error";
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;//nese ekziston json dhe ka fushen message vtm ath e merr mesazhin

                // 401/403/422/429 -> i shfaqim si danger/warning
                if (xhr.status === 429) showLoginAlert(msg, "warning");
                else showLoginAlert(msg, "danger");
            }
        });
    });

});
