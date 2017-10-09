/**
 * Created by FPT Software on 08/02/2017.
 */
var cloneAnswerRow = '<div class="answerRow col-md-12 col-sm-12 col-xs-12">';
cloneAnswerRow += '<div class="row">';
cloneAnswerRow += '<div class="answerIndex col-md-1 col-sm-1 col-xs-1 col-md-offset-1 ">⭕</div>';
cloneAnswerRow += '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">';
cloneAnswerRow += '<input class="answerInput col-md-10 col-sm-10 col-xs-10" placeholder="回答を入力してください"/>';
cloneAnswerRow += '<div class="controlButton col-md-1 col-sm-1 col-xs-2">';
cloneAnswerRow += '<a class="addAnswer" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.addAnswer(this)"><span class=" glyphicon glyphicon-plus add"></span></a>';
cloneAnswerRow += '<a class="remove" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.removeAnswer(this)"><span class="pull-right glyphicon glyphicon-minus remove"></span></a>';
cloneAnswerRow += '</div>';
cloneAnswerRow += '</div>';
cloneAnswerRow += '</div>';
cloneAnswerRow += '</div>';

var cloneNormalQuestion = '<div class="view " hidden>';
cloneNormalQuestion += '<div class="col-md-12">';
cloneNormalQuestion += '<div class="heighthr"></div>';
cloneNormalQuestion += '<div class="row">';
cloneNormalQuestion += '<h5 class="col-md-2 col-md-12">質問<span class="index">1</span></h5>';
cloneNormalQuestion += '<div class="col-md-10 col-sm-12 col-xs-12 none-distance">';
cloneNormalQuestion += '<div class="btn-space col-md-7 col-sm-12 col-xs-12 box-text" >本施策について施策の印象をお聞かせください。</div>';
cloneNormalQuestion += '<div class="btn-space col-md-offset-1 fixd-padding-right col-md-1 col-sm-2 col-xs-12">';
cloneNormalQuestion += '<button onclick="NOTICEMANAGEMENT.editQuestion(this)" type="button" name="type-not-question" class="btn btn-success col-md-12 col-sm-12 col-xs-12 pull-info" >修正</button>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '<div class="btn-space fixd-padding-right col-md-1 col-sm-2 col-xs-12">';
cloneNormalQuestion += '<button onclick="NOTICEMANAGEMENT.delQuestion(this)" type="button" class="btn btn-success col-md-12 col-sm-12 col-xs-12 pull-right" >削除</button>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '<div class="btn-space col-md-2 col-sm-3 col-xs-12 ">';
cloneNormalQuestion += '<select onchange="NOTICEMANAGEMENT.addNewQuestion(this)" class="form-control btn btn-success">';
cloneNormalQuestion += '<option disabled="" selected="" hidden="" value="0">新質問追加</option>';
cloneNormalQuestion += '<option class="type-bg-white" value="1">選択式</option>';
cloneNormalQuestion += '<option class="type-bg-white" value="2">自由記入式</option>';
cloneNormalQuestion += '</select>';
cloneNormalQuestion += '</div>';

cloneNormalQuestion += '</div>';
cloneNormalQuestion += '<div class="col-md-offset-2">';
cloneNormalQuestion += '<div class="errorHidden btn-space col-md-7 col-sm-12 col-xs-12">';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '<div class="edit questionRow">';
cloneNormalQuestion += '<div class="col-md-12">';
cloneNormalQuestion += '<div class="row">';
cloneNormalQuestion += '<h5 class="col-md-2">質問<span class="index">1</span></h5>';
cloneNormalQuestion += '<div class="col-md-10 col-sm-10 col-xs-12 fixd-padding-left">';
cloneNormalQuestion += '<input class="questionType" value="0" type="hidden"/>';
cloneNormalQuestion += '<textarea onkeyup="COMMON.textAreaAdjust(this,74)" class="questionContent col-md-12 col-sm-12 col-xs-12" placeholder="質問を入力してください" rows="2"></textarea>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '<div class="col-md-10 col-sm-10 col-xs-12 pull-right">';
cloneNormalQuestion += '<div class="row">';
cloneNormalQuestion += '<div class="col-md-2 col-md-offset-8 col-sm-4 col-xs-12 fixd-padding-right">';
cloneNormalQuestion += '<button onclick="NOTICEMANAGEMENT.cancelSaveQuestion(this)" type="button" name="type-question" class="btnCancelSave tempQuestion btn btn-space btn-default col-md-11 col-sm-12 col-xs-12 pull-right" >キャンセル</button>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '<div class="col-md-2 col-sm-4 col-xs-12">';
cloneNormalQuestion += '<button onclick="NOTICEMANAGEMENT.saveQuestion(this)" name="type-question" type="button"  class="btn btn-space btn-primary col-md-12 col-sm-12 col-xs-12 pull-right" >保存</button>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';
cloneNormalQuestion += '</div>';

var cloneAnswerQuestion = '<div class="view" hidden>';
cloneAnswerQuestion += '<div class="col-md-12">';
cloneAnswerQuestion += '<div class="heighthr" ></div>';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<h5 class="col-md-2">質問<span class="index">2</span></h5>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-12 col-xs-12 none-distance">';
cloneAnswerQuestion += '<div class="col-md-7 box-text btn-space" >本施策について施策の印象をお聞かせください。</div>';
cloneAnswerQuestion += '<div class="btn-space col-md-offset-1 fixd-padding-right col-md-1 col-sm-2 col-xs-12">';
cloneAnswerQuestion += '<button onclick="NOTICEMANAGEMENT.editQuestion(this)" type="button" name="type-not-question" class="btn btn-success col-md-12 col-sm-12 col-xs-12 pull-info" >修正</button>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="btn-space fixd-padding-right col-md-1 col-sm-2 col-xs-12">';
cloneAnswerQuestion += '<button onclick="NOTICEMANAGEMENT.delQuestion(this)" type="button" class="btn btn-success col-md-12 col-sm-12 col-xs-12 pull-right" >削除</button>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="btn-space col-md-2 col-sm-3 col-xs-12 ">';
cloneAnswerQuestion += '<select onchange="NOTICEMANAGEMENT.addNewQuestion(this)" class="form-control btn btn-success">';
cloneAnswerQuestion += '<option disabled="" selected="" hidden="" value="0">新質問追加</option>';
cloneAnswerQuestion += '<option class="type-bg-white" value="1">選択式</option>';
cloneAnswerQuestion += '<option class="type-bg-white" value="2">自由記入式</option>';
cloneAnswerQuestion += '</select>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="col-md-offset-2">';
cloneAnswerQuestion += '<div class="errorHidden btn-space col-md-7 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="edit questionRow">';
cloneAnswerQuestion += '<div class="col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<h5 class="col-md-2 col-sm-2 col-xs-12">質問<span class="index">2</span></h5>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-12 fixd-padding-left">';
cloneAnswerQuestion += '<input class="questionType" value="1" type="hidden"/>';
cloneAnswerQuestion += '<textarea onkeyup="COMMON.textAreaAdjust(this,74)" class="questionContent col-md-12 col-sm-12 col-xs-12" placeholder="質問を入力してください" rows="2"></textarea>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<h5 class="col-md-10 col-sm-10 col-xs-12 col-md-offset-2 col-sm-offset-2" style="padding: 0">';
cloneAnswerQuestion += '<label class="form-check-input">';
cloneAnswerQuestion += '<input id="cbTypeAnswer" onchange="NOTICEMANAGEMENT.setMultiAnswer(this);" type="checkbox">';
cloneAnswerQuestion += '<label for="cbTypeAnswer"></label> 複数回答を許可する　※チェックボックス使用';
cloneAnswerQuestion += '</label>';
cloneAnswerQuestion += '</h5>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div style="padding-left: 0" class="errorContainer col-md-offset-2 col-md-10 col-sm-12 col-xs-12"></div>';
cloneAnswerQuestion += '<div class="answerRow col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<div class="answerIndex col-md-1 col-sm-1 col-xs-1 col-md-offset-1 ">⭕</div>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">';
cloneAnswerQuestion += '<input class="answerInput col-md-10 col-sm-10 col-xs-10" placeholder="回答を入力してください"/>';
cloneAnswerQuestion += '<div controlButton class="col-md-1 col-sm-1 col-xs-2">';
cloneAnswerQuestion += '<a class="addAnswer" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.addAnswer(this)"><span class=" glyphicon glyphicon-plus add"></span></a>';
cloneAnswerQuestion += '<a class="remove" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.removeAnswer(this)"><span class="pull-right glyphicon glyphicon-minus remove"></span></a>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="answerRow col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<div class="answerIndex col-md-1 col-sm-1 col-xs-1 col-md-offset-1 ">⭕</div>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">';
cloneAnswerQuestion += '<input class="answerInput col-md-10 col-sm-10 col-xs-10 " placeholder="回答を入力してください"/>';
cloneAnswerQuestion += '<div class="controlButton col-md-1 col-sm-1 col-xs-2">';
cloneAnswerQuestion += '<a class="addAnswer" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.addAnswer(this)"><span class=" glyphicon glyphicon-plus add"></span></a>';
cloneAnswerQuestion += '<a class="remove" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.removeAnswer(this)"><span class="pull-right glyphicon glyphicon-minus remove"></span></a>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="answerRow col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<div class="answerIndex col-md-1 col-sm-1 col-xs-1 col-md-offset-1 ">⭕</div>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">';
cloneAnswerQuestion += '<input class="answerInput col-md-10 col-sm-10 col-xs-10 " placeholder="回答を入力してください"/>';
cloneAnswerQuestion += '<div class="controlButton col-md-1 col-sm-1 col-xs-2">';
cloneAnswerQuestion += '<a class="addAnswer" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.addAnswer(this)"><span class=" glyphicon glyphicon-plus add"></span></a>';
cloneAnswerQuestion += '<a class="remove" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.removeAnswer(this)"><span class="pull-right glyphicon glyphicon-minus remove"></span></a>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="answerRow col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<div class="answerIndex col-md-1 col-sm-1 col-xs-1 col-md-offset-1 ">⭕</div>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">';
cloneAnswerQuestion += '<input class="answerInput col-md-10 col-sm-10 col-xs-10 " placeholder="回答を入力してください"/>';
cloneAnswerQuestion += '<div class="controlButton col-md-1 col-sm-1 col-xs-2">';
cloneAnswerQuestion += '<a class="addAnswer" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.addAnswer(this)"><span class=" glyphicon glyphicon-plus add"></span></a>';
cloneAnswerQuestion += '<a class="remove" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.removeAnswer(this)"><span class="pull-right glyphicon glyphicon-minus remove"></span></a>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="answerRow col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<div class="answerIndex col-md-1 col-sm-1 col-xs-1 col-md-offset-1 ">⭕</div>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">';
cloneAnswerQuestion += '<input class="answerInput col-md-10 col-sm-10 col-xs-10 " placeholder="回答を入力してください"/>';
cloneAnswerQuestion += '<div class="controlButton col-md-1 col-sm-1 col-xs-2">';
cloneAnswerQuestion += '<a class="addAnswer" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.addAnswer(this)"><span class=" glyphicon glyphicon-plus add"></span></a>';
cloneAnswerQuestion += '<a class="remove" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.removeAnswer(this)"><span class="pull-right glyphicon glyphicon-minus remove"></span></a>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="answerRow col-md-12 col-sm-12 col-xs-12">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<div class="answerIndex col-md-1 col-sm-1 col-xs-1 col-md-offset-1 ">⭕</div>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-10 none-distance">';
cloneAnswerQuestion += '<input class="answerInput col-md-10 col-sm-10 col-xs-10 " placeholder="回答を入力してください"/>';
cloneAnswerQuestion += '<div class="controlButton col-md-1 col-sm-1 col-xs-2">';
cloneAnswerQuestion += '<a class="addAnswer" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.addAnswer(this)"><span class=" glyphicon glyphicon-plus add"></span></a>';
cloneAnswerQuestion += '<a class="remove" href="javascript:void(0)" onclick="NOTICEMANAGEMENT.removeAnswer(this)"><span class="pull-right glyphicon glyphicon-minus remove"></span></a>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="col-md-10 col-sm-10 col-xs-12 pull-right">';
cloneAnswerQuestion += '<div class="row">';
cloneAnswerQuestion += '<div class="col-md-2 col-md-offset-8 col-sm-4 col-xs-12 fixd-padding-right">';
cloneAnswerQuestion += '<button onclick="NOTICEMANAGEMENT.cancelSaveQuestion(this)" type="button" name="type-question" class="btnCancelSave tempQuestion btn btn-space btn-default col-md-11 col-sm-12 col-xs-12 pull-right" >キャンセル</button>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '<div class="col-md-2 col-sm-4 col-xs-12">';
cloneAnswerQuestion += '<button onclick="NOTICEMANAGEMENT.saveQuestion(this)" name="type-question" type="button"  class="btn btn-space btn-primary col-md-12 col-sm-12 col-xs-12 pull-right" >保存</button>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';
cloneAnswerQuestion += '</div>';


var NOTICEMANAGEMENT = {
    NAME_STORAGE: 'NOTICE_ITEMS',
    init: function () {
        window.NAME_STORAGE = NOTICEMANAGEMENT.NAME_STORAGE;

        $("#table-list-notice > tbody > tr").click(function () {
            $(this).find('input[name="rowTab1[]"]').click();
        });

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
            pickerPosition: "top-right"
        });
        $('#datepicker1').datetimepicker({
            format: 'yyyy/mm/dd',
            weekStart: true,
            todayBtn: true,
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
            startView: 2,
            minView: 2,
            forceParse: false,
            pickerPosition: "top-right"
        });

        $('#createView .view').show();
        $('#createView .edit').hide();

        $('input[name="rowTab1[]"]').click(function () {
            COMMON.checkRowTable(this);
        });

        COMMON.setCheckboxFromLocalStorage(NOTICEMANAGEMENT.NAME_STORAGE);
        function onActive(item) {
            $(".menu-sort li").removeClass('active');
            $(item).addClass('active');
        }

        $('input[name="rowTab1[]"]').click(function () {
            var typeSet = $(this).is(':checked') ? 'add' : 'remove';
            if (typeSet == 'add') {
                $('#row-notice-' + $(this).val()).addClass('active');
            } else {
                $('#row-notice-' + $(this).val()).removeClass('active');
            }
        });

        $('#checkbox-all').on('click', function () {
            var checked = this.checked;
            $('input[name="rowTab1[]"]').each(function (i, obj) {
                $(obj).prop('checked', checked);
                var typeSet = checked ? 'add' : 'remove';
                if (typeSet == 'add') {
                    $('#row-notice-' + $(this).val()).addClass('active');
                } else {
                    $('#row-notice-' + $(this).val()).removeClass('active');
                }
            });
            
            if(checked){
                $('#table-list-notice>tbody>tr').addClass('active');
            }else $('#table-list-notice>tbody>tr').removeClass('active');
        });

        NOTICEMANAGEMENT.resetIndex();
        COMMON.autoCheckedCheckboxAll();
    },

    ajaxSubmitForm: function(type){
        $('#createView').find('label.error').remove();
        NOTICEMANAGEMENT.noticeValidateCustomMethod();
        $("#form-input-notice-"+type).validate({
            ignore: [],
            rules: {
                noticeTitle: {
                    required: true,
                    maxlength: 500
                },
                noticeDescription: {
                    required: true,
                    maxlength: 1000
                },
                noticeFirstPublicDate: {
                    required: true
                },
                noticeLastPublicDate: {
                    required: true
                },
                noticeFirstPublicDate: {
                    checkFormatDate: true,
                    required: true
                },
                noticeLastPublicDate: {
                    checkFormatDate: true,
                    required: true,
                    compareLastDateWithFirstDate: true,
                }
            },

            submitHandler: function (form,event) {
                COMMON.confirm_message('登録確認ください', function(){
                    // Remove null answer input before save
                    $('.questionRow').each(function(){
                        $(this).find('.answerInput').each(function(i){
                            if ($(this).val() == '') {
                                $(this).remove();
                            }
                        })
                    });

                    // Save Data via AJAX
                    $.ajax({
                        type:"POST",
                        url:"/administrator/notice-management/save-notice-"+type,
                        data: $(form).serialize(),
                        success: function (data) {
                            window.location.href = '/administrator/notice-management/review-notice/'+data.noticeId + '/' + type;
                        }
                    });
                }, '', '登録確認','確認','キャンセル','fncClose');

                return false;
            },

            errorPlacement: function (error, element) {
                if (element.hasClass('noticeFirstPublicDate')) {
                    error.appendTo('#error1');
                } else if(element.hasClass('noticeLastPublicDate')){
                    error.appendTo('#error2');
                } else if(element.hasClass('answerInput')){
                    element.parent().parent().parent().parent().parent().find('.view').hide();
                    element.parent().parent().parent().parent().show();
                    if (!element.parent().parent().parent().parent().find('.errorContainer').html()) {
                        element.parent().parent().parent().parent().find('.errorContainer').html(error);
                    }
                } else if(element.hasClass('questionContent')){
                    element.parent().parent().parent().parent().parent().find('.view').hide();
                    element.parent().parent().parent().parent().show();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            showErrors: function(errorMap, errorList) {
                this.defaultShowErrors();
            }
        });
        
        

        var countQuestion = $('#createView').find('.question').length;

        for (var i = 1; i <= countQuestion; i++) {
            $("textarea[name*='questions["+i+"][content]']").rules("add", {
              maxlength: 1000,
              required:true
            });
            var parent = $("textarea[name*='questions["+i+"][content]']").parent().parent().parent().parent();
            if (parent.find('.answerInput').length > 0) {
                for (var j = 0; j <= 5; j++) {
                    $("input[name='questions["+i+"][answers]["+j+"]']").rules("add", {
                      maxlength: 500,
                      answerGroup:true
                    });
                }
            }
        }

        $('#form-input-notice-'+type).submit();
    },
    noticeValidateCustomMethod: function (){
        $.validator.addMethod("compareLastDateWithFirstDate", function (value, element) {
            var startPublishDate = $('#noticeFirstPublicDate').val();
            var endPublishDate = $('#noticeLastPublicDate').val();
            if (value) {
                if (startPublishDate && endPublishDate) {
                    if (value < startPublishDate || value > endPublishDate) return false;
                } else if (startPublishDate) {
                    if (value < startPublishDate) return false;
                } else if (endPublishDate) {
                    if (value > endPublishDate) return false;
                }
            }
            return true;
        },"Last publish date must be greater than first publish date");

        $.validator.addMethod("answerGroup", function (value, element) {
            var parent = $(element).parent().parent().parent().parent();
            var length = parent.find('.answerInput').length;
            var index = parent.find('.index').html();
            for (var i = 0; i <= length; i++) {
                if ($("input[name='questions["+index+"][answers]["+i+"]']").val()) {
                    return true;
                }        
            }
            return false;
        },"少なくとも1つの回答を入力してください");

        $.extend($.validator.messages, {  
            maxlength: $.validator.format("半角で{0}文字以内で入力してください。")
        });
    },
    publicNotice: function() {
        var storeValue = sessionStorage.getItem(NOTICEMANAGEMENT.NAME_STORAGE);
        if (storeValue && JSON.parse(storeValue).length > 0) {
            COMMON.confirm_message('登録確認ください', function(){
                $.ajax({
                    type:"POST",
                    url:"/administrator/notice-management/public-notice",
                    data: {'notices':sessionStorage.getItem(NOTICEMANAGEMENT.NAME_STORAGE)},
                    success: function () {
                        NOTICEMANAGEMENT.removeAllItemsFromLocalStorage();
                        COMMON.popupMessage('fdsfsda', function () {
                            window.location.reload();
                        }, 'fslkdjflsd', '', '', function () {
                            window.location.reload();
                        });
                    }
                });
            }, '', '登録確認','確認','キャンセル','fncClose');
        } else {
            COMMON.warningPopup('選択項目にチェックを入れてください。', '');
        }
    },
    privateNotice: function() {
        var storeValue = sessionStorage.getItem(NOTICEMANAGEMENT.NAME_STORAGE);
        if (storeValue && JSON.parse(storeValue).length > 0) {
            COMMON.confirm_message('登録確認ください', function(){
                $.ajax({
                    type:"POST",
                    url:"/administrator/notice-management/private-notice",
                    data: {'notices':sessionStorage.getItem(NOTICEMANAGEMENT.NAME_STORAGE)},
                    success: function () {
                        NOTICEMANAGEMENT.removeAllItemsFromLocalStorage();
                        COMMON.popupMessage('fdsfsdfds', function () {
                            window.location.reload();
                        }, 'fslkdjflsd', '', '', function () {
                            window.location.reload();
                        });
                    }
                });
            }, '', '登録確認','確認','キャンセル','fncClose');
        } else {
            COMMON.warningPopup('選択項目にチェックを入れてください。', '');
        }
    },
    deleteNotice: function() {
        COMMON.confirm_message('選択項目にチェックを入れてください。', function(){
            $.ajax({
                type:"POST",
                url:"/administrator/notice-management/delete-notice",
                data: {'notices':sessionStorage.getItem(NOTICEMANAGEMENT.NAME_STORAGE)},
                success: function () {
                    NOTICEMANAGEMENT.removeAllItemsFromLocalStorage();
                    window.location.reload();
                }
            });
        }, '', '注意','確認','キャンセル','fncClose');
    },
    addAnswer: function (element) {
        var parent = $(element).parent().parent().parent().parent();
        if ($('.answerRow', parent.parent()).length < 6) {
            $(parent).after(cloneAnswerRow);
        }
        console.log($(parent));
        NOTICEMANAGEMENT.resetIndex();
        NOTICEMANAGEMENT.isMaxAnswer();
    },
    removeAnswer: function (item) {
        if ($(item).parent().parent().parent().parent().parent().find('.answerRow').length > 1) {
            $(item).parent().parent().parent().parent().remove();
        }
        NOTICEMANAGEMENT.resetAnswerIndex();
        NOTICEMANAGEMENT.isMaxAnswer();
    },
    saveQuestion: function (item) {
        if ($(item).parent().parent().find('.btnCancelSave').hasClass('tempQuestion')) {
            $(item).parent().parent().find('.btnCancelSave').removeClass('tempQuestion');
        }
        if(item.name==='type-question'){
            $(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.edit').hide();
            $(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.view').show();
            $(item).parent().parent().parent().parent().find('.answerInput').each(function(){
                if ($(this).val() == '') {
                    if ($(item).parent().parent().parent().parent().find('.answerInput').length > 1) {
                        $(this).parent().parent().parent().remove();
                    }
                }

            });
        }
        if(item.name==='type-not-question'){
            $(item).parent().parent().parent().parent().parent().find('div.edit').hide();
            $(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.view').show();
        }
        NOTICEMANAGEMENT.resetIndex();
    },
    cancelSaveQuestion: function (item) {
        if ($(item).hasClass('tempQuestion')) {
            $(item).parent().parent().parent().parent().parent().remove();
        }
        if(item.name==='type-question'){
            $(item).parent().parent().parent().parent().parent().find('div.edit').hide().find('input').val('');
            $(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.view').show();
        }
        if(item.name==='type-not-question'){
            $(item).parent().parent().parent().parent().parent().find('div.edit').hide().find('input').val('');
            $(item).parent().parent().parent().parent().parent().removeClass('box-text-outline').find('div.view').show();
        }
        if($('#flagCheck').val()=='cancel') {
            $(item).parent().parent().parent().parent().parent().remove();
            NOTICEMANAGEMENT.resetIndex();
        }
        NOTICEMANAGEMENT.resetIndex();
    },
    addNewQuestion: function (item) {
        var valSelect = $(item).children("option:selected" ).val();
        if ($('#createView').find('.question').length < 50) {
            if(valSelect === '1'){
                $(item).parent().parent().parent().parent().parent().parent().after('<div class="form-group box-text-outline question">'+cloneAnswerQuestion+'</div>');
            }
            if(valSelect === '2'){
                $(item).parent().parent().parent().parent().parent().parent().after('<div class="form-group box-text-outline question">' + cloneNormalQuestion + '</div>');
            }
            NOTICEMANAGEMENT.resetIndex();
            $(item).children('option[value=0]').prop("selected", true);
        }
    },
    editQuestion: function (item) {
        if(item.name==='type-question'){
            $(item).parent().parent().parent().parent().parent().parent().addClass('box-text-outline').find('div.edit').show();
            $(item).parent().parent().parent().parent().parent().parent().find('div.view').hide();
        }
        if(item.name==='type-not-question'){
            $(item).parent().parent().parent().parent().parent().parent().addClass('box-text-outline').find('div.edit').show();
            $(item).parent().parent().parent().parent().parent().parent().find('div.view').hide();
        }
        $('#flagCheck').val('cancel');
        NOTICEMANAGEMENT.resetIndex();
    },
    delQuestion: function (item) {
        if ($('#createView').find('.question').length > 1) {
            COMMON.confirm_message("削除を確認しますか？",function () {
                $(item).parent().parent().parent().parent().parent().parent().remove();
                NOTICEMANAGEMENT.resetIndex();
            })
        }
    },
    resetIndex: function (){
        $("#createView .form-group").each(function (i, obj) {
            if($(obj).find(".index").length>0) {
                $(obj).find(".index").html(++i);
            }
        });

        $('.questionRow').each(function(){
            var index = $(this).find('.index').html();
            $(this).find('textarea').attr('name', 'questions['+index+'][content]');
            $(this).find('.questionType').attr('name', 'questions['+index+'][questionType]');
            $(this).find('.answerInput').each(function(i,obj){
                $(this).attr('data-answer-group',index);
                $(this).attr('name','questions['+index+'][answers]['+i+']');
            })

            $(this).find('.answerRow').each(function(i){
                $(this).find('.answerIndex').html(parseInt(i) + 1);
            })
        })
        NOTICEMANAGEMENT.resetAnswerIndex();
        NOTICEMANAGEMENT.isMaxAnswer();
    },
    resetAnswerIndex: function (){
        $('.questionRow').each(function(){
            var index = $(this).find('.index').html();
            $(this).find('.answerRow').each(function(i){
                $(this).find('.answerIndex').html(parseInt(i) + 1);
            })
        })
    },
    isMaxAnswer: function (){
        $('.questionRow').each(function(){
            if ($(this).find('.answerRow').length == 6) {
                $(this).find('.answerRow').each(function(i,obj){
                    $(obj).find('.add').removeClass('glyphicon-plus');
                    $(obj).find('.add').addClass('glyphicon-pencil');
                    $(obj).find('.addAnswer').attr('onclick','NOTICEMANAGEMENT.clearAnswerInputRow(this)');
                })
            } else {
                $(this).find('.answerRow').each(function(i,obj){
                    $(obj).find('.add').removeClass('glyphicon-pencil');
                    $(obj).find('.add').addClass('glyphicon-plus');
                    $(obj).find('.addAnswer').attr('onclick','NOTICEMANAGEMENT.addAnswer(this)')
                })
            }
        })
    },
    clearAnswerInputRow:function (element){
        $(element).parent().parent().find('.answerInput').val('');
    },
    setMultiAnswer: function (element){
        if ($(element).is(':checked')) {
            $(element).parent().parent().parent().parent().parent().find('input.questionType').val('2');
        } else {
            $(element).parent().parent().parent().parent().parent().find('input.questionType').val('1');
        }
    },
    searchNotice: function (){
        NOTICEMANAGEMENT.removeAllItemsFromLocalStorage()
        $('#search-notice').submit();
    },
    removeAllItemsFromLocalStorage: function () {
        sessionStorage.removeItem(NOTICEMANAGEMENT.NAME_STORAGE);
    },
}   