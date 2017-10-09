/**
 * Created by FPT Software on 02/02/2017.
 */

var REGISTER = {
    init: function init() {
        $("#register-form").validate({
            rules:{
                email:{
                    required: true,
                    email: true,
                    maxlength: 256
                },
                confirmEmail:{
                    required: true,
                    equalTo: "#txtEmail"
                },
                password: {
                    required: true,
                    passwordValidation: true,
                    minlength: 8
                },
                confirmPassword: {
                    required: true,
                    equalTo: "#txtPassword"
                },
                questionId: {
                    required: true
                },
                answer: {
                    required: true,
                    checkFullSizeWithLength: 10
                }
            },
            messages: {
                email: {
                    maxlength: 'メールが半角の256文字以下であり、メールのフォーマットが正しいこと。'
                },
                password: {
                    minlength: REGISTER.messageList.MSG_LG_047_WrongPassFormat
                },
                confirmEmail: {
                    equalTo: REGISTER.messageList.MSG_LG_049_ConfirmEmailWrong
                },
                confirmPassword: {
                    equalTo: REGISTER.messageList.MSG_LG_048_ConfirmNewPassWrong
                }
            },
            submitHandler: function (form, event) {
                // do other things for a valid form
                form.submit();
            }
        });
    },
}
