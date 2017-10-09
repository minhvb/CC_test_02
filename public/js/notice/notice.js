$(document).ready(function () {
    NOTICE.init();
});
var NOTICE = {
	init: function () {
		$('input[name*="answer["]').each(function () {
		    var questionId = $(this).attr('data-id');
		    if (questionId) {
		    	if ($(this).attr('type') == 'radio') {
		    		$(this).click(function(){
			            sessionStorage.removeItem('question_' + questionId);
			            COMMON.setItemToLocalStorage($(this).val(), 'add', 'question_' + questionId);
			        });
			        NOTICE.setRadioFromLocalStorage('question_' + questionId, questionId);
		    	} else if($(this).attr('type') == 'checkbox'){
		    		$(this).click(function(){
		    			NOTICE.checkAllRowTable($(this), 'question_' + questionId);
		    		});
		    		NOTICE.setCheckboxFromLocalStorage('question_' + questionId, questionId);
		    	}
		    }
		});

		$('textarea').each(function () {
		    var questionId = $(this).attr('data-id');
		    if (questionId) {
		        $(this).bind('input propertychange', function() {
		            sessionStorage.removeItem('question_' + questionId);
		            if ($(this).val()) {
		            	COMMON.setItemToLocalStorage($(this).val(), 'add', 'question_' + questionId);
		            }
		        });
		        NOTICE.setTextAreaFromLocalStorage('question_' + questionId, questionId);
		    }
		});

		$('#surveyForm').validate();
	    $('[name*="answer_"]').each(function() {
	        $(this).rules('add', { required: true });
	    });
    },
    closeNoticeWindow: function () {
    	window.open('','_parent','');
    	window.close();
    },
    setCheckboxFromLocalStorage: function (nameStorage, question) {
        var items = COMMON.getItemsFromLocalStorage(nameStorage);
        if (items) {
            items.forEach(function (item) {
                $('input[name="answer['+question+']"][value="' + item + '"]').prop('checked', 'checked');
            });
            COMMON.autoCheckedCheckboxAll();
        }
        $('#checkbox-selected-first').html(items.length + '件の選択した施策を');
        $('#checkbox-selected-last').html(items.length + '件の選択した施策を');
    },
    setRadioFromLocalStorage: function (nameStorage,answer) {
        var items = COMMON.getItemsFromLocalStorage(nameStorage);
        if (items) {
            items.forEach(function (item) {
                $('input[name="answer['+answer+']"][value="' + item + '"]').prop('checked', 'checked');
            });
        }
    },
    setTextAreaFromLocalStorage: function (nameStorage,answer) {
        var items = COMMON.getItemsFromLocalStorage(nameStorage);
        if (items) {
            items.forEach(function (item) {
                $('#textField_'+answer).val(item);
            });
        }
    },
    submitSurvey: function () {
    	var totalQuestion = $('input[name="totalResults"]').val();
    	var answers = {};
    	console.log(sessionStorage);
		for(var i in sessionStorage) {
			if (i.match(/question_.*/)) {
			    answer = JSON.parse(sessionStorage.getItem(i));
			    answers[i] = answer;
			}
		}
		console.log(answers);
		console.log(Object.keys(answers).length);
		if (Object.keys(answers).length == totalQuestion) {
			// Set Message And Submit Action to Default
			$('.regCompleteTitle').html('アンケート登録完了');
			$('.regCompleteBody').html('アンケートを登録しました。 ご協力ありがとうございました。');
			$('.regCompleteFooter button').attr('onclick','NOTICE.closeNoticeWindow()');
			//Submit Data via AJAX
			$.ajax({
	            type:"POST",
	            url:"/notification/save",
	            data:{
	            	answers:answers,
	            	noticeId: $('input[name="noticeId"]').val(),
	            	surveyId: $('input[name="surveyId"]').val()
	            },
	            success: function () {
                    NOTICE.removeAllItemsFromLocalStorage();
                }
           	});
		} else {
			// Show Error Message
			$('.regCompleteTitle').html('');
			$('.regCompleteBody').html('日本語で質問を全て回答して頂けないでしょうか。');
			$('.regCompleteFooter button').attr('onclick','');
		}
    },
    removeAllItemsFromLocalStorage:function (){
    	$('*[name*="answer"]').each(function(){
    		sessionStorage.removeItem('question_'+$(this).attr('data-id'));
    	});
    },
    checkAllRowTable: function (e, nameStorage) {
    	if ($(e).is(':checked')) {
        	var typeSet = 'add';
    	} else {
    		var typeSet = 'remove';
    	}
        COMMON.setItemToLocalStorage($(e).val(), typeSet, nameStorage);
        var itemsLength = COMMON.getItemsFromLocalStorage(nameStorage).length;
        $('#checkbox-selected-first').html(itemsLength + '件の選択した施策を');
        $('#checkbox-selected-last').html(itemsLength + '件の選択した施策を');
    }
}