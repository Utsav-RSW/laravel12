$(document).ready(function () {
    $("#current_pwd").keyup(function () {
        var current_pwd = $(this).val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "/admin/verify-password",
            data: { current_pwd: current_pwd },
            success: function (response) {
                if (response == "false") {
                    $("#verifyPwd").html("<font color='red'>Current Password is incorrect</font>");
                } else {
                    $("#verifyPwd").html("<font color='green'>Current Password is correct</font>");
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });
})