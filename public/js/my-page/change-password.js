/**
 * Created by FPT Software on 02/02/2017.
 */
var CHANGE_PASSWORD = {
    validateForm: false,
    init: function init() {
        CHANGE_PASSWORD.validateForm = $("#frmChangePassword").validate({
            rules: {
                oldPassword:{
                    required: true
                },
                newPassword: {
                    required: true,
                    passwordValidation: true,
                    minlength: 8
                },
                newPasswordRetype: {
                    required: true,
                    equalTo: "#newPassword"
                },
            },
            messages: {
                newPassword: {
                    minlength: "英数混在の8文字以上で入力してください。"
                },
                newPasswordRetype: {
                    equalTo: "新しいパスワードと新しいパスワード（確認）が一致しません。"
                }
            },
            submitHandler: function (form, event) {
                // do other things for a valid form
                CHANGE_PASSWORD.submitPassword(form);
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
                    if (data.status) {
                        $('#changePasswordModal').modal('hide');
                        $('#successChangePasswordModal').modal({backdrop: 'static'});
                    } else {
                        var errorContent = "<ul>";
                        data.message.forEach(function (error) {
                            errorContent += "<li>" + error + "</li>"
                        });

                        errorContent += "</ul>";
                        $("#errors-list-change-password").html(errorContent);
                    }
                },
                error: function (err) {

                }
            }
        );
    }
}
