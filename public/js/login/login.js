/**
 * Created by FPT Software on 02/02/2017.
 */

var LOGIN = {
    messageList: null,
    init: function () {
        $("#login-form").validate({
            rules:{
                username: "required",
                password: "required"
            },
            submitHandler: function(form) {
                // do other things for a valid form
                form.submit();
            }
        });
    }
}

