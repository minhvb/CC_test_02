/**
 * Created by FPT Software on 02/02/2017.
 */

var NEW_PASSWORD = {
    URL_SUBMIT: '/forgot-password/update-password',
    init: function init() {
        $("#submit-form").validate({
            rules: {
                oldPassword:{
                    required: true
                },
                newPassword: {
                    required: true,
                    passwordValidation: true,
                    minlength: 8
                },
                confirmPassword: {
                    required: true,
                    equalTo: "#txtNewPassword"
                }
            },
            messages: {
                newPassword: {
                    minlength: NEW_PASSWORD.messageList.MSG_LG_047_WrongPassFormat
                },
                confirmPassword: {
                    equalTo: NEW_PASSWORD.messageList.MSG_LG_048_ConfirmNewPassWrong
                }
            },
            submitHandler: function (form, event) {
                // do other things for a valid form
                NEW_PASSWORD.submitPassword(form);
                event.preventDefault ? event.preventDefault() : (event.returnValue = false);
                return false;
            }
        });
    },
    submitPassword: function submit(form) {
        $.ajax({
                type: 'post',
                url: $(form).attr('action'),
                data: $(form).serialize(),
                success: function (data) {
                    if (data.success) {
                        $('#confirm-success-dialog').modal('show');
                        $('#btnFinish').focus();
                        $("#errors-list").html('');
                    } else {
                        var errorContent = "<ul>";
                        data.errors.forEach(function (error) {
                            errorContent += "<li>" + error + "</li>"
                        });

                        errorContent += "</ul>";
                        $("#errors-list").html(errorContent);
                    }
                },
                error: function (err) {

                }
            }
        );
    }
}
