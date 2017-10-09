var SURVEY = {
	nameStorage: 'POLICY_ITEMS',
    initLoad: function () {
    	window.NAME_STORAGE = SURVEY.nameStorage;
    	$('.publishStartdate').datetimepicker({
    		format: 'yyyy-mm-dd',
    		weekStart: true,
    		todayBtn: true,
    		clearBtn: true,
    		autoclose: true,
    		todayHighlight: true,
    		startView: 2,
    		minView: 2,
    		forceParse: false,
    	});
    	$('.publishEnddate').datetimepicker({
    		format: 'yyyy-mm-dd',
    		weekStart: true,
    		todayBtn: true,
    		clearBtn: true,
    		autoclose: true,
    		todayHighlight: true,
    		startView: 2,
    		minView: 2,
    		forceParse: false,
    	});
    	$("#table-list-policy > tbody > tr").click(function () {
            $(this).find('input[name="rowTab1[]"]').click();
        });
    	$('input[name="rowTab1[]"]').click(function () {
            $(this).click();
            /*var typeSet = $(this).is(':checked') ? 'add' : 'remove';
            if (typeSet == 'add') {
                $('#box-policy-' + $(this).val()).addClass('active');
            } else {
                $('#box-policy-' + $(this).val()).removeClass('active');
            }*/
            //POLICY.setItemToLocalStorage($(this).val(), typeSet);
            //var itemsLength = POLICY.getItemsFromLocalStorage().length;
            //$('#checkbox-selected-first').html(itemsLength + '件の選択した施策を');
            //$('#checkbox-selected-last').html(itemsLength + '件の選択した施策を');
        });
    	$('#checkbox-all').on('click', function () {
	        var checked = this.checked;
	        $('input[name="rowTab1[]"]').each(function (i, obj) {
	            $(obj).prop('checked', checked);
	            var typeSet = checked ? 'add' : 'remove';
	            //POLICY.setItemToLocalStorage($(obj).val(), typeSet);
	        });
	        //var itemsLength = POLICY.getItemsFromLocalStorage().length;
	        //$('#checkbox-selected-first').html(itemsLength + '件の選択した施策を');
	        //$('#checkbox-selected-last').html(itemsLength + '件の選択した施策を');
	    });
    },
    searchPolicy: function () {
        $('#search-policy').submit();
    }
   
}