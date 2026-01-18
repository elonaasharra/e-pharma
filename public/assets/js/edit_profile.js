$(function () {

    $("#editProfileForm").on("submit", function (e) {
        e.preventDefault();

        var name = $("#name").val().trim();
        var surname = $("#surname").val().trim();
        var alpha = /^[a-zA-Z]{2,40}$/;
        var error = 0;

        $("#name_message").text("");
        $("#surname_message").text("");

        if (!alpha.test(name)) {
            $("#name_message").text("Invalid name");
            error++;
        }

        if (!alpha.test(surname)) {
            $("#surname_message").text("Invalid surname");
            error++;
        }

        if (error > 0) return;

        var data = new FormData(document.getElementById("editProfileForm"));
        data.append("action", "update_profile");

        $.ajax({
            type: "POST",
            url: "/e-pharma/public/ajax/ajax_update_profile.php",
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (res) {
                alert(res.message);
                if (res.location) {
                    window.location.href = res.location;
                }
            },
            error: function () {
                alert("AJAX error");
            }
        });
    });

});
