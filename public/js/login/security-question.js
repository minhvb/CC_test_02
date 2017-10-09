/**
 * Created by FPT Software on 02/02/2017.
 */

var SECURITY_QUESTION = {
    init: function init() {
        $("#submit-form").validate({
            rules: {
                questionId: "required",
                answer: {
                    required: true,
                    checkFullSizeWithLength: 10
                }
            },
            submitHandler: function (form, event) {
                // do other things for a valid form
                form.submit();
            }
        });
    }
}
