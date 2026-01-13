

console.log("register.js loaded");
// dmth qe kodi te ekzekutohet vetem pasi faqja te jete ngarkuar
$(document).ready(function () {
    //funksion ndihmes qe kontrollon nese nje input eshte bosh
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

        // reset
        $(".form-control").removeClass("border-danger");
        $("span.text-danger").text("");

//VALIDIME FRONT END

        // Name
        if (!alpha_regex.test(name)) {
            $("#name").addClass("border-danger");
            $("#name_message").text("Name must contain only letters (min 3).");
            error++;
        }

        // Surname
        if (!alpha_regex.test(surname)) {
            $("#surname").addClass("border-danger");
            $("#surname_message").text("Surname must contain only letters (min 3).");
            error++;
        }

        // Email
        if (!email_regex.test(email)) {
            $("#email").addClass("border-danger");
            $("#email_message").text("Invalid email format.");
            error++;
        }

        // Password
        if (isEmpty(password) || password.length < 8) {
            $("#password").addClass("border-danger");
            $("#password_message").text("Password must be at least 8 characters.");
            error++;
        }

        // Confirm password
        if (confirm_password !== password) {
            $("#confirm_password").addClass("border-danger");
            $("#confirm_password_message").text("Passwords do not match.");
            error++;
        }

        if (error > 0) return;

        //pergatisim js qe te dergoj te dhena ne backend

        let data = new FormData();
        data.append("action", "register");   // shumë e rëndësishme
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
            success: function (response) {
                console.log(response);
                alert(response.message + (response.error ? ("\n" + response.error) : ""));
            }
            ,
            error: function () {
                alert("AJAX error");
            }

        });

    });

});
