/**
 * Created by FPT Software on 02/02/2017.
 */
var RESET_PASSWORD = {
    validateForm: false,
    init: function init() {          
        RESET_PASSWORD.validateForm = $("#frmResetPassword").validate({
            rules: {
                newPassword: {
                    required: true,
                    passwordValidation: true,
                    minlength: 8
                }
            },
            messages: {
                newPassword: {
                    minlength: "英数混在の8文字以上で入力してください。"
                }
            },
            submitHandler: function (form, event) {
                // do other things for a valid form
                event.preventDefault ? event.preventDefault() : (event.returnValue = false);

                $('#resetPasswordModal').modal('hide');
                $('#resetPasswordConfirmModal .message').html($(form).find("#usernameResetPassword").val() + 'のユーザーIDのパスワードをリセットすることを確認してください。');               
                $('#resetPasswordConfirmModal').modal({backdrop: 'static'});
                return false;
            }
        });
    },
    submitPassword: function submit(form) {
        $.ajax({
            type: 'post',
            url: $(form).attr('action'),
            data: $(form).serialize(),
            success: function (response) {
                $('#resetPasswordConfirmModal').modal('hide');
                $('#resetPasswordSuccessModal .message').html(response.message);               
                $('#resetPasswordSuccessModal').modal({backdrop: 'static'});
            },
            error: function (err) {
//                $('#resetPasswordErrorModal .message').html("エラーが発生します。エラーが再発生する場合、管理者に連絡してください。");
            }
        });
    }
}


