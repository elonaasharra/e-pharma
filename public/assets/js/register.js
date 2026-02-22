console.log("register.js loaded"); //perdorim per debug , per te par nese file esht ngarkuar apo jo ne browser

$(document).ready(function () { // e perdorim ne kete menyre qe kodi te ekzekutohet vetem kur faqja te jet ngarkuar plotesisht

    function showRegisterAlert(msg, type) {// shfaq msg suksesi ose gabimi te divi me id registerAlert
        $("#registerAlert")
            .removeClass("d-none alert-success alert-danger alert-warning alert-info")
            .addClass("alert-" + type)
            .text(msg);
    }

    function isEmpty(val) {
        return !val || val.trim() === "";  // trim heq hapsirat para dhe pas tekstit
                                           // nese esht bosh kthen true nese jo kthen false
    }

    $("#registerForm").on("submit", function (e) {    // on submit te formes me id registerForm ekzekutohet ky funksion
        e.preventDefault();                                // ndalon rifreskimin automatik te faqes
      // marrim vlerat nga inputet qe jan vendos te forma duke i kapur me id perkatese , .val lexon vleren qe esht shkruajtur ne input
        let name = $("#name").val();
        let surname = $("#surname").val();
        let email = $("#email").val();
        let password = $("#password").val();
        let confirm_password = $("#confirm_password").val();

        let email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // rregullat per kontrollin e emailit
        let alpha_regex = /^[a-zA-Z]{3,40}$/;
        let error = 0; // numerohen sa gabime ka , sepse ne fun kontrollohet dhe nese ka te pakten nje gabim formulari nuk dergohet ne server

        // pastrojm gabimet dhe mesazhet e vjetra nga ekrani para se te behet validimi i ri
        $("#registerAlert").addClass("d-none").text("").removeClass("alert-success alert-danger alert-warning alert-info");
        $(".form-control").removeClass("border-danger");
        $("span.text-danger").text("");

        // VALIDIME FRONTEND
        if (!alpha_regex.test(name)) {
            $("#name").addClass("border-danger");
            $("#name_message").text("Name must contain only letters (min 3).");  // tekstet vendosen ne span
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
        // pergatisim te dhenat qe do te dergojm ne server

        let data = new FormData(); // krijon nje objekt qe mban te dhenat qe do dergohen
        data.append("action", "register"); // komand qe serveri ta kuptoj qe po bejm regjistrim
        data.append("name", name);   // shtojm vlerat qe perdoruesi ka shkruar ne ekran
        data.append("surname", surname);
        data.append("email", email);
        data.append("password", password);
        data.append("confirm_password", confirm_password);
 // te dhenat dergohen me post ne ajax
        $.ajax({  // komunikim me serverin pa rifreskuar faqen
            type: "POST",  // te dhenat dergohen me metoden post
            url: "ajax/ajax_register.php",  // ky esht serveri qe do perpunoj te dhenat
            processData: false, // i thot serverit tmos e konvertoj ne tekst
            contentType: false, // le browserin ta vendosi vet formatin e dergimit
            data: data,
            dataType: "json",

            success: function (response) {
                if (typeof response === "string") response = JSON.parse(response); // JavaScript Object Notation  , menyr per ti shkruar inf ne formen celes :vlere

                showRegisterAlert(
                    response.message || "Registration completed successfully.", // nese serveri ka derguar mesazh perdoret ai , nese jo perdoret ky automatiku
                    response.error ? "danger" : "success" // nese ka gabime e kuqe , nese ska jeshile
                );

                // reset form pas suksesit
                if (!response.error) {
                    $("#registerForm")[0].reset();
                }
            },

            error: function (xhr) {   // ekzekutohet kur kemi probleme komunikimi me serverin
                let msg = "AJAX error"; // ktu vendosim nje msg fillestar
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;// nese serveri ka kthyer mesazh gabimi nejson ai e mer ate dhe e shfaq
                }
                showRegisterAlert(msg, "danger");
            }
        });

    });

});
