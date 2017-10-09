/**
 * Created by FPT Software on 02/02/2017.
 */

var FORGOT_PASSWORD = {
    URL_VALIDATE_EMAIL: 'forgot-password/check-email',
    init: function init() {
        $("#forgot-form").validate({
            rules: {
                username: "required",
                questionId: "required",
                answer: "required"
            },
            submitHandler: function (form, event) {
                // do other things for a valid form
                form.submit();
            }
        });
    },
    checkEmail: function submit(username) {
        $.ajax({
                type: 'post',
                url: FORGOT_PASSWORD.URL_VALIDATE_EMAIL,
                data: {'username': username},
                success: function (data) {
                    if (data.success) {
                        $('#question-form').show();
                        $("#errors-list").html('');
                    } else {
                        $("#cbMailFlag").attr('checked', false);
                        $('#question-form').hide();

                        var errorContent = "<ul>";
                        data.errors.forEach(function (error) {
                            errorContent += "<li>" + error + "</li>"
                        });

                        errorContent += "</ul>";
                        $("#errors-list").html(errorContent);
                    }
                },
                error: function (err) {
                    $("#cbMailFlag").attr('checked', false);
                }
            }
        );
    },
    onMailExist: function (element) {
        if ($("#cbMailFlag").is(':checked')) {
            var result = $("#forgot-form").validate().element("#txtUsername");
            $("#errors-list").html('');
            if (result) {
                FORGOT_PASSWORD.checkEmail($('#txtUsername').val());
            } else {
                $("#cbMailFlag").attr('checked', false);
            }
        } else {
            $('#question-form').hide();
        }
    },
    preLoad: function (element) {
        if ($("#cbMailFlag").is(':checked')) {
            $('#question-form').show();
        } else {
            $('#question-form').hide();
        }
    }
}
