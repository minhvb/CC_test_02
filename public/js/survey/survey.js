/**
 * Created by FPT Software on 02/13/2017.
 */
var SURVEY = {
	surveyId: null,
	policyId: null,
	totalQ: null,
	listQId: null,
	URL_ADDS_VOTE: '/survey/addSvote',
	inputChecked: function(){
		$('input[type=radio]').change(function() {
            var itemId = this.value;
            var nameStorage = this.name;
            var type = 'add';
            var divId = this.id;
            var valueStorage = COMMON.getItemsFromLocalStorage(nameStorage+'_question');
            COMMON.setItemToLocalStorage(valueStorage, "remove", nameStorage+'_question');
            jQuery.each( valueStorage, function( i, val ) {
            	COMMON.setItemToLocalStorage(val, "remove", nameStorage+'_question');
			});
            
            var idStorage = COMMON.getItemsFromLocalStorage(nameStorage+'_id');
            COMMON.setItemToLocalStorage(idStorage, "remove", nameStorage+'_id');
            jQuery.each( idStorage, function( i, val ) {
            	COMMON.setItemToLocalStorage(val, "remove", nameStorage+'_id');
			});
            
            COMMON.setItemToLocalStorage(itemId, type, nameStorage+'_question');
            COMMON.setItemToLocalStorage(divId, type, nameStorage+'_id');
        });
		$('textarea').focusout(function(){
	        var textareaId = this.id;
	        var text = $('textarea#'+textareaId).val();
	        var nameStorage = textareaId;
	        var dataId = $(this).attr('data-id');
	        var type = 'add';
	        var valueStorage = COMMON.getItemsFromLocalStorage(nameStorage+'_question');
            COMMON.setItemToLocalStorage(valueStorage, "remove", nameStorage+'_question');
            jQuery.each( valueStorage, function( i, val ) {
            	COMMON.setItemToLocalStorage(val, "remove", nameStorage+'_question');
			});
	        
	        var valueStorage = COMMON.getItemsFromLocalStorage(nameStorage+'_text');
            COMMON.setItemToLocalStorage(valueStorage, "remove", nameStorage+'_text');
            jQuery.each( valueStorage, function( i, val ) {
            	COMMON.setItemToLocalStorage(val, "remove", nameStorage+'_text');
			});
            
            COMMON.setItemToLocalStorage(text, type, nameStorage+'_question');
            COMMON.setItemToLocalStorage(dataId, type, nameStorage+'_text');
	    });
    },
    addlistQId: function(){
    	var bfListQId = COMMON.getItemsFromLocalStorage('listQId');
    	if($(SURVEY.listQId).not(bfListQId).length > 0 ){
    		jQuery.each( SURVEY.listQId, function( i, val ) {
    			COMMON.setItemToLocalStorage(val, 'add', 'listQId');
			});
    	}
    	if( bfListQId.length > 0 && SURVEY.listQId != null ){
	    	if( SURVEY.listQId.length > 0 ){
	    		jQuery.each( SURVEY.listQId, function( i, val ) {
	    			if( COMMON.getItemsFromLocalStorage('question_'+val+'_id').length > 0 ){
	    				var divId = COMMON.getItemsFromLocalStorage('question_'+val+'_id')[0];
		    			$('input[type=radio]#'+divId).prop("checked", true);
	    			}
	    			if( COMMON.getItemsFromLocalStorage('question_'+val+'_text').length > 0 ){
	    				var text = COMMON.getItemsFromLocalStorage('question_'+val+'_question')[0];
		    			$('textarea#'+'question_'+val).val( text );
	    			}
				});
	    	}
    	}
    	if(typeof(Storage) !== "undefined") {
    		var goback = sessionStorage.getItem('goback');
    		if( goback != null ){
    			sessionStorage.setItem('goback', goback - 1);
    		}else{
    			sessionStorage.setItem('goback', '-3');
    		}
    	}
    },
    requestVote: function(){
    	$('.requestVote').click(function(){
    		var dataQuestion = [];
            var bfListQId = COMMON.getItemsFromLocalStorage('listQId');
            var checkAll = true;
            
            jQuery.each( bfListQId, function( i, val ) {
            	var iQuestion = COMMON.getItemsFromLocalStorage('question_'+val+'_question');
            	var iAnswer = COMMON.getItemsFromLocalStorage('question_'+val+'_id');
            	var iText = COMMON.getItemsFromLocalStorage('question_'+val+'_text');
            	if( iQuestion.length > 0 ){
            		if( iAnswer.length >0 ){
	            		var rsiAnswer = iAnswer[0].split('_');
	            		dataQuestion[i] = {'key':val, 'value': null, 'answer' : rsiAnswer[1]};
            		} else if( iText.length > 0 ){
	            		dataQuestion[i] = {'key':val, 'value':iQuestion[0], 'answer' : null};
            		} else{
            			checkAll = false;
                		return false;
            		}
            	}else{
            		checkAll = false;
            		return false;
            	}
			});
            if( checkAll && ( SURVEY.totalQ === bfListQId.length ) && bfListQId.length >0 ){
            	var dataPost = {surveyId: SURVEY.surveyId, policyId: SURVEY.policyId, question: dataQuestion};
            	$.ajax({
                    type: 'POST',
                    url: SURVEY.URL_ADDS_VOTE,
                    data: dataPost,
                    dataType: 'json',
                    success: function (data) {
                        if ( data.success ) {
                        	$('#pleaseWaitDialog').modal('hide');
                        	$('#dialogSucceed').modal({
                        		backdrop: 'static',
                        		keyboard: false,
                        		show: true
                        	});
                        	if(typeof(Storage) !== "undefined") {
            	            	sessionStorage.removeItem('listQId');
            	            	jQuery.each( bfListQId, function( i, val ) {
            	            		sessionStorage.removeItem('question_'+val+'_question');
            	            		sessionStorage.removeItem('question_'+val+'_id');
            	            		sessionStorage.removeItem('question_'+val+'_text');
            	            		$('textarea#'+'question_'+val).val( '' );
            	    			});
                        	}
                        }
                    }
                });
            }else{
            	$('#pleaseWaitDialog').modal('hide');
            	$('#pleaseWaitDialogFail').modal('show');
            }
            
    	});
    }
}