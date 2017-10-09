var htmlContentQuesion = '<div class="col-md-12 col-sm-12 col-xs-12">' +
						 	'<div class="row">'+
							    '<h5 class="col-md-2 col-sm-2 col-xs-12">質問<span class="index">2</span></h5>'+
							    '<div class="col-md-10 col-sm-10 col-xs-12">'+
							    	'<textarea name="questions[]" class="questionsTxt col-md-12 col-sm-12 col-xs-12" placeholder="質問を入力してください" rows="2"></textarea>'+
							    '</div>'+
							'</div>'+
						'</div>';
var htmlAswChoice = '<div class="answerRow col-md-12 col-sm-12 col-xs-12">'+
					    '<div class="row">'+
						    '<div class="col-md-1 col-sm-1 col-xs-1 col-md-offset-1 numAnsRow">2</div>'+
						    '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">'+
							 	'<input name="answers[]" class="answerInput col-md-10 col-sm-10 col-xs-10" placeholder="回答を入力してください"/>'+
							 	'<div class="col-md-1 col-sm-1 col-xs-2">'+
								    '<a class="add" href="javascript:void(0)" onclick="SURVEY_QUESTIONS.addAnswer(this)">'+
								    	'<span class="glyphicon glyphicon-plus"></span>'+
								    '</a>'+
								    '<a class="remove" href="javascript:void(0)" onclick="SURVEY_QUESTIONS.removeAnswer(this)">'+
								    	'<span class="pull-right glyphicon glyphicon-minus remove"></span>'+
								    '</a>'+
							    '</div>'+
							'</div>'+
						    '<div class="col-md-offset-2 col-sm-offset-1 col-xs-offset-1 col-md-10 col-sm-10 col-xs-10 answerInputRow"></div>'+
						'</div>'+
					'</div>';
var htmlView = 	'<div class="view" hidden>'+
				    '<div class="col-md-12">'+
				        '<div class="heighthr"></div>'+
				        '<div class="row">'+
					        '<h5 class="col-md-2 col-md-12">質問<span class="index">1</span></h5>'+
					        '<div class="col-md-10 col-sm-12 col-xs-12 none-distance">'+
						        '<div class="btn-space col-md-7 col-sm-12 col-xs-12 box-text" >本施策について施策の印象をお聞かせください。</div>'+
						        '<div class="btn-space col-md-2 col-sm-3 col-xs-12 pull-right">'+
							        '<select onchange="SURVEY_QUESTIONS.addNewQuestion(this)" class="form-control btn btn-success">'+
								        '<option disabled="" selected="" hidden="" value="0">新質問追加</option>'+
								        '<option class="type-bg-white" value="1">選択式</option>'+
								        '<option class="type-bg-white" value="2">自由記入式</option>'+
							        '</select>'+
						        '</div>'+
						        '<div class="btn-space fixd-padding-right col-md-1 col-sm-2 col-xs-12 pull-right">'+
						        	'<button onclick="SURVEY_QUESTIONS.delQuestion(this)" type="button" class="btn btn-success col-md-12 col-sm-12 col-xs-12 pull-right" >削除</button>'+
						        '</div>'+
						        '<div class="btn-space fixd-padding-right col-md-1 col-sm-2 col-xs-12 pull-right">'+
						        	'<button onclick="SURVEY_QUESTIONS.editQuestion(this)" type="button" name="type-not-question" class="btn btn-success col-md-12 col-sm-12 col-xs-12 pull-info" >修正</button>'+
						        '</div>'+
					        '</div>'+
				        '</div>'+
				        '<div class="heighthr"></div>'+
				    '</div>'+
				'</div>';
var SURVEY_QUESTIONS = {
	URL_LIST_POLICY: '/administrator/survey-management/index',
	dataQuestions:[],
	validatorQuestions: false,
	countQuestion: 2,
	htmlViewNotAswQuestion: htmlView +
        '<div class="edit questionRow">'+ htmlContentQuesion +
        	'<input class="typeQuestion" name="typeQuestion" value="0" type="hidden"/>'+
	        '<div class="col-md-10 col-sm-10 col-xs-12 pull-right">'+
		        '<div class="row">'+
			        '<div class="col-md-2 col-sm-4 col-xs-12 pull-right">'+
			        	'<button onclick="SURVEY_QUESTIONS.save(this)" name="type-not-question" type="button" class="btn btn-space btn-primary col-md-12 col-sm-12 col-xs-12 pull-right" >保存</button>'+
			        '</div>'+
			        '<div class="col-md-2 col-sm-4 col-xs-12 pull-right">'+
			        	'<button onclick="SURVEY_QUESTIONS.cancelSave(this)" name="type-not-question" type="button" class="btn btn-space btn-default col-md-12 col-sm-12 col-xs-12 pull-right" >キャンセル</button>'+
			        '</div>'+
		        '</div>'+
	        '</div>'+
        '</div>',
    htmlViewQuestion: htmlView +
        '<div class="edit questionRow">'+ htmlContentQuesion+
        	'<input class="typeQuestion" name="typeQuestion" value="1" type="hidden"/>'+
	        '<div class="heighthr"></div>'+
	        '<div class="col-md-12 col-sm-12 col-xs-12">'+
		        '<div class="row">'+
		        	'<h5 class="col-md-12 col-sm-12 col-xs-12">'+
			        	'<label class="form-check-input">'+
				        	'<input class="changeType" name="changeType" id="cbSelect" value="1" type="checkbox">'+
				        	'<label for="cbSelect"></label>'+　
				        	'複数回答を許可する　※チェックボックス使用'+
			        	'</label>'+
		        	'</h5>'+
		        '</div>'+
	        '</div>'+ htmlAswChoice + htmlAswChoice + htmlAswChoice + htmlAswChoice + htmlAswChoice + htmlAswChoice +
	        '<div class="col-md-10 col-sm-10 col-xs-12 pull-right">'+
		        '<div class="row">'+
			        '<div class="col-md-2 col-sm-4 col-xs-12 pull-right">'+
			        	'<button onclick="SURVEY_QUESTIONS.save(this)" name="type-question" type="button"  class="btn btn-space btn-primary col-md-12 col-sm-12 col-xs-12 pull-right" >保存</button>'+
			        '</div>'+
			        '<div class="col-md-2 col-sm-4 col-xs-12 pull-right">'+
			        	'<button onclick="SURVEY_QUESTIONS.cancelSave(this)" type="button" name="type-question" class="btn btn-space btn-default col-md-12 col-sm-12 col-xs-12 pull-right" >キャンセル</button>'+
			        '</div>'+
		        '</div>'+
	        '</div>'+
        '</div>',
	initLoad:function(){
	    $('#datepicker').datetimepicker({
            format: 'yyyy/mm/dd',
            weekStart: true,
            todayBtn: true,
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
            startView: 2,
            minView: 2,
            forceParse: false,
        }).find("input").val(SURVEY_QUESTIONS.getFormattedDate(new Date()));
	    
	    $('#createView').html('<div class="form-group">'+SURVEY_QUESTIONS.htmlViewNotAswQuestion+'</div>'+'<div class="form-group">'+SURVEY_QUESTIONS.htmlViewQuestion+'</div>');
	    $('#createView .view').show();
	    $('#createView .edit').hide();
	    
	    $.validator.addMethod("checkFormatDateTime", function (value, element) {
            return COMMON.isCorrectFormatDate(value);
        }, 'Error_Publish_Date_Format');
	    
	    SURVEY_QUESTIONS.validatorQuestions = $("#form-policy-survey").validate({
	    	ignore: '',
	    	rules: {
	    		createDate: {
	    			required: true,
                    checkFormatDateTime: true
                },
                questions: {
	    			required: true
                },
            },
            errorPlacement: function (error, element) {
                if ( element.hasClass('createDate') ) {
                	$('#errorCreateDate').show();
                	error.appendTo('#errorCreateDate');
                } else if( element.hasClass('answerInput') ){
                	error.appendTo(element.parent().parent().find('.answerInputRow'));
                } else if( element.hasClass('questionsTxt') ){
                	error.insertAfter(element);
                	$(element).parent().parent().parent().parent().parent().find('.edit').show();
                } else{
                	error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
            	COMMON.confirm_message("content confirm submit", function () {
            		SURVEY_QUESTIONS.submitSurvey(form);
                }, '', 'title confirm submit', '確認', 'キャンセル');
                return false;
            },
            invalidHandler: function (form, validator) {
            	var errors = validator.numberOfInvalids();
            }
	    });
	    SURVEY_QUESTIONS.setIndex();
	},
	setIndex:function () {
        $("#createView .form-group").each(function (i, obj) {
            if($(obj).find(".index").length>0) {
                $(obj).find(".index").html(++i);
            }
            SURVEY_QUESTIONS.countQuestion=i;
            $(this).find('textarea').attr('name', 'questions['+i+'][content]');
            $(this).find('textarea').rules("add", {
        		required: true
            });
            $(this).find('.typeQuestion').attr('name', 'questions['+i+'][typeQuestion]');
            $(this).find('.changeType').attr('name', 'questions['+i+'][changeType]');
            
            $(this).find('.answerRow').not('.hidden').each(function (indexRow, ansObjRow) {
            	if($(ansObjRow).find(".numAnsRow").length>0) {
                    $(ansObjRow).find(".numAnsRow").html( indexRow + 1 );
                }
            });
            $(this).find('.answerInput').each(function (index, ansObj) {
            	$(ansObj).attr('name','questions['+i+'][answers]['+index+']');
            	$(ansObj).rules("add", {
            		required: true
                });
            });
        });
    },
	editQuestion:function(item){
		$(item).parent().parent().parent().parent().parent().parent().addClass('box-text-outline').find('div.edit').show();
        $(item).parent().parent().parent().parent().parent().parent().find('div.view').hide();
        //get data form-group
        var changeType = null;
        if($(item).parent().parent().parent().parent().parent().parent().find(".changeType").is(':checked')){
        	$(item).parent().parent().parent().parent().parent().parent().find(".changeType").val(2);
        	changeType = 2;
        }
        var answerInput = [];
        $(item).parent().parent().parent().parent().parent().parent().find(".answerInput").each(function(i, obj){
        	answerInput[i] = obj.value;
        });
        var indexQuestion = parseInt($(item).parent().parent().parent().parent().parent().parent().find(".index").html());
        SURVEY_QUESTIONS.dataQuestions[indexQuestion] = {
        	'txtQ':  $(item).parent().parent().parent().parent().parent().parent().find('textarea').val(),
        	'changeType': changeType,
        	'answerInput': answerInput
        };
        SURVEY_QUESTIONS.setIndex();
	},
    cancelSave:function (item) {
    	$(item).parent().parent().parent().parent().parent().find('div.edit').hide();
    	$(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.view').show();
    	
    	var indexQuestion = parseInt($(item).parent().parent().parent().parent().parent().find(".index").html());
    	if( SURVEY_QUESTIONS.dataQuestions[indexQuestion] != undefined ){
    		$(item).parent().parent().parent().parent().parent().find('textarea').val(SURVEY_QUESTIONS.dataQuestions[indexQuestion]['txtQ']);
    		$(item).parent().parent().parent().parent().parent().find('.answerInput ').each(function(i, obj){
    			$(obj).val(SURVEY_QUESTIONS.dataQuestions[indexQuestion]['answerInput'][i]);
    		});
    		$(item).parent().parent().parent().parent().parent().find('.changeType').prop("checked", SURVEY_QUESTIONS.dataQuestions[indexQuestion]['changeType']);
    	}
    	$(item).parent().parent().parent().parent().find('.answerRow').removeClass('hidden');
    	$(item).parent().parent().parent().parent().find('.addNewAns').remove();
    	SURVEY_QUESTIONS.setIndex();
    },
    save:function (item) {
    	$(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.edit').hide();
        $(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.view').show();
        
        var cntQuestion = $(item).parent().parent().parent().parent().find('textarea').val();
        $(item).parent().parent().parent().parent().parent().find('.box-text').text(cntQuestion);
        
        $(item).parent().parent().parent().parent().find('.hidden').remove();
        SURVEY_QUESTIONS.setIndex();
    },
    addNewQuestion:function (item) {
    	SURVEY_QUESTIONS.countQuestion++;
        var valSelect =$(item).children("option:selected" ).val();
        if(valSelect==='1'){
            $(item).parent().parent().parent().parent().parent().parent().after('<div class="form-group box-text-outline">'+SURVEY_QUESTIONS.htmlViewQuestion+'</div>');
        }
        if(valSelect==='2'){
            $(item).parent().parent().parent().parent().parent().parent().after('<div class="form-group box-text-outline">'+SURVEY_QUESTIONS.htmlViewNotAswQuestion+'</div>');
        }
        SURVEY_QUESTIONS.setIndex();
        $(item).children('option[value=0]').prop("selected", true);
    },
    delQuestion:function (item) {
        COMMON.confirm_message("削除を確認しますか？",function () {
            $(item).parent().parent().parent().parent().parent().parent().remove();
            SURVEY_QUESTIONS.setIndex();
        });
    },
    removeAnswer:function (item) {
        if ($(item).parent().parent().parent().parent().parent().find('.answerRow').length > 1) {
            $(item).parent().parent().parent().parent().addClass('hidden');//after remove or show
        }
        SURVEY_QUESTIONS.setIndex();
    },
    addAnswer:function (element) {
        var parent = $(element).parent().parent().parent().parent();
        if (($('.answerRow', parent.parent()).length - $('.answerRow.hidden', parent.parent()).length) < 6) {
            $(parent).after(htmlAswChoice.replace('answerRow', 'answerRow addNewAns'));
        }
        SURVEY_QUESTIONS.setIndex();
    },
    submitSurvey: function (form) {
        $(form).ajaxSubmit({
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                	SURVEY_QUESTIONS.redirectToListPolicy;
                } else {
                    //
                }
            }
        });
    },
    redirectToListPolicy: function () {
        window.location = SURVEY_QUESTIONS.URL_LIST_POLICY;
        return;
    },
    getFormattedDate: function (date) {
        var _year = date.getFullYear();
        var _month = (1 + date.getMonth()).toString();
        _month = _month.length > 1 ? _month : '0' + _month;
        var _day = date.getDate().toString();
        _day = _day.length > 1 ? _day : '0' + _day;
        return _year + '/' + _month + '/' + _day;
    },
}