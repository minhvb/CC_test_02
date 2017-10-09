/**
 * Created by FPT Software on 02/13/2017.
 */
var POLICY_DETAIL = {
	policyId : null,
	userId: null,
	URL_ADDF_POLICY: '/policy/addFpolicy',
    addFPolicy: function () {
        $('.FPolicy').click(function () {
        	var className = $('.FPolicy').attr('class');
        	var class_current = 'removeFPolicy';
        	var class_reverse = 'addFPolicy';
        	var class_icon = 'icon-star';
        	var txt_text = 'お気に入りに登録';
        	var class_btn_c = 'btn-success';
        	var class_btn_r = 'btn-warning';
        	if(className.indexOf('addFPolicy') != -1){
        		class_current = 'addFPolicy';
        		class_reverse = 'removeFPolicy';
        		txt_text = 'お気に入りから削除';
        		class_btn_c = 'btn-warning';
        		class_btn_r = 'btn-success';
        	}
            var dataPost = {policyId: POLICY_DETAIL.policyId, userId: POLICY_DETAIL.userId };
            $.ajax({
                type: 'POST',
                url: POLICY_DETAIL.URL_ADDF_POLICY,
                data: dataPost,
                dataType: 'json',
                success: function (data) {
                    if ( data.success ) {
                    	if(data.flag.add == 1 || data.flag.remove == 1 ){
	                    	$('.FPolicy').addClass(class_reverse + ' ' + class_btn_c);
	                    	$('.FPolicy').removeClass(class_current);
	                    	$('.FPolicy').removeClass(class_btn_r);
	                    	$('.FPolicy').html('<span class="'+class_icon+'"></span>'+ txt_text);
                    	}
                    	if(data.flag.add == 1){
                    		$('#popupDialogSuccess').modal({
                    			backdrop: 'static',
                        		keyboard: false,
                        		show: true
                        	});
                    	}
                    	if(data.flag.add == 0){
                    		$('#popupDialogStop').modal({
                    			backdrop: 'static',
                        		keyboard: false,
                        		show: true
                        	});
                    	}
                    	if(data.flag.remove == 1){
                    		$('#popupDialogUnF').modal({
                    			backdrop: 'static',
                        		keyboard: false,
                        		show: true
                        	});
                    	}
                    } else{
                    	// alert();
                    }
                },
            });
        });
    },
    goBack: function(){
    	if(typeof(Storage) !== "undefined") {
	    	var goback = sessionStorage.getItem('goback');
	    	if( goback != null ){
	    		sessionStorage.removeItem('goback');
	    		history.go(goback);
	    	}else{
	    		history.go(-1);
	    	}
    	}else{
    		// brownser not support
    		history.go(-1);
    	}
    }
}