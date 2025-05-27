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

    $(document).on('click', '#deleteProfileImage', function () {
        if(confirm("Are you sure you want to delete this profile image?")) {
            var adminId = $(this).data('admin-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "delete-profile-image",
                data: { admin_id: adminId },
                success: function (resp) {
                    if (resp['status'] == true) {
                        alert(resp['message']);
                        $("#profileImageBlock").remove();
                    }
                },
                error: function () {
                    alert("Error occurred while deleting profile image");
                }
            });
        }
    });
})