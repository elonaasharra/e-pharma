$(document).ready(function () {
    $("#forgotForm").on("submit", function (e) {
        e.preventDefault();

        let email = $("#fp_email").val().trim();
        let email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        $("#fp_email").removeClass("border-danger");
        $("#fp_email_message").text("");

        if (!email_regex.test(email)) {
            $("#fp_email").addClass("border-danger");
            $("#fp_email_message").text("Invalid email format");
            return;
        }

        let data = new FormData();
        data.append("action", "forgot_password");
        data.append("email", email);

        $.ajax({
            type: "POST",
            url: "ajax/ajax_forgot_password.php",
            dataType: "json",
            processData: false,
            contentType: false,
            data: data,
            success: function (response) {
                alert(response.message);
            },
            error: function () {
                alert("AJAX error");
            }
        });
    });
});
