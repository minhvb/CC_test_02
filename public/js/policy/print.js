/**
 * Created by FPT Software on 02/13/2017.
 */
var PRINT = {
	URL_TOTAL_PRINT: '/policy/addTotalPrint',
    initLoad: function (id) {
        $('.print').click(function () {
        	window.print();
        	$.ajax({
                type: 'POST',
                url: PRINT.URL_TOTAL_PRINT,
                data: {'policyId': id},
                dataType: 'json',
                success: function (data) {
                    return true;
                }
            });
        });
    },
}