/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

         
$(document).ready(function () {
    COMMON.init();
    $('.modal').on('hidden.bs.modal', function (e) {
        if($('.modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }    
    });
    var rx = /INPUT|SELECT|TEXTAREA/i;
    $(document).bind("keydown keypress", function(e){
        if( e.which == 8 ){ // 8 == backspace
            if(!rx.test(e.target.tagName) || e.target.disabled || e.target.readOnly ){
                return false;
            }
        }
    });
    
    $(document).on({
        'show.bs.modal': function () {
            var zIndex = 1040 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
            setTimeout(function() {
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        },
        'hidden.bs.modal': function() {
            if ($('.modal:visible').length > 0) {
                // restore the modal-open class to the body element, so that scrolling works
                // properly after de-stacking a modal.
                setTimeout(function() {
                    $(document.body).addClass('modal-open');
                }, 0);
            }
        }
    }, '.modal');

    $(document).ajaxStart(function () {
        $("#full-screen").show();
    });
    
    $( document ).ajaxStop(function() {
        setTimeout(function () {
            $("#full-screen").hide();
        }, 300);
    });
});
var COMMON = {
    DATE_FORMAT: 'Y/m/d',
    DATE_TIME_FORMAT: 'Y/m/d H:i',
    urlCheckPaword: '/application/home/change-password',
    init: function () {

        var delayMillis = 1; //1 second
        setTimeout(function() {
            $('.container-body .table-hover>tbody').find('input[name="rowTab1[]"]:checked').parent().parent().addClass('active');
        }, delayMillis);

        //create placeholder for IE9
        if(COMMON.isIE9()) {
            $('input, textarea').placeholder();
        }

        $(document).ajaxError(function (event, jqxhr, settings, thrownError) {
            if(jqxhr.status == 402){
                window.location.href = 'login';
                return false;
            }
            if(jqxhr.status == 500){
                COMMON.errorPopup('エラーが発生します。エラーが再発生する場合、管理者に連絡してください。');
            }
        });

        $.fn.datetimepicker.dates['en'] = {
            days: ["日", "月", "火", "水", "木", "金", "土"],
            daysShort: ["日", "月", "火", "水", "木", "金", "土"],
            daysMin: ["日", "月", "火", "水", "木", "金", "土"],
            months: ["１月", "２月", "３月", "４月", "５月", "６月", "７月", "８月", "９月", "１０月", "１１月", "１２月"],
            monthsShort: ["１月", "２月", "３月", "４月", "５月", "６月", "７月", "８月", "９月", "１０月", "１１月", "１２月"],
            today: "今日",
            clear: "クリア",
            meridiem: ['am', 'pm'],
            suffix: ['st', 'nd', 'rd', 'th']
        };

        // init jquery validator
        COMMON.initJqueryValidator();

        $("input:visible, textarea:visible, select:visible").first().focus();

        $("form").submit(function () {
            $(this).closest('form').find('.error-box').html('');
        });

        $('input[name="rowTab1[]"]').click(function () {
            COMMON.checkRowTable(this);
        });
    },
    initJqueryValidator: function () {
        // config jquery validation
        $.validator.setDefaults({
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            onfocusout: false,
            onkeyup: false,
            onclick: false,
            ignore: ':hidden:not([type="password"])',
        });

        $.validator.addMethod("checkFullSize", function (value) {
            var lengtKanji = value.replace(/^\s+|\s+$/g, '');
            if (/(?:[a-zA-Z0-9-_\'!@#$%^&*()\uff5F-\uff9F\u0020])/.test(lengtKanji) == true && lengtKanji.length > 0) {
                return false;
            } else {
                if (/[\u0020]/.test(lengtKanji) == true) {
                    return false;
                }
            }
            return true;
        }, "全角で文字以内で入力してください。");

        $.validator.addMethod("checkFullSizeWithLength", function (value, element, params) {
            if (!value) {
                return true;
            }
            var length = $.isArray(value) ? value.length : this.getLength(value, element);
            if (length > params) {
                return false;
            }
            var lengtKanji = value.replace(/^\s+|\s+$/g, '');
            if (/(?:[a-zA-Z0-9-_\'!@#$%^&*.\/;:<>|?{}\[\]()\uff5F-\uff9F\u0020])/.test(lengtKanji) == true && lengtKanji.length > 0) {
                return false;
            } else {
                if (/[\u0020]/.test(lengtKanji) == true) {
                    return false;
                }
            }
            return true;
        }, "全角で{0}文字以内で入力してください。");

        $.validator.addMethod("halfSize", function (value) {
            var regex = /^[a-zA-Z0-9-_!@#$%^&*\/\/:.+="'()\uFF61-\uFFDC\uFFE8-\uFFEE]*$/;
            if (value.length > 0 && regex.test(value)) {
                return true;
            }
            return false;
        }, "半角で文字以内で入力してください。");

        $.validator.addMethod("checkHalfSizeWithLength", function (value, element, params) {
            if (!value) {
                return true;
            }
            var length = $.isArray(value) ? value.length : this.getLength(value, element);
            if (length > params) {
                return false;
            }
            var regex = /^[a-zA-Z0-9-_?!@#$%^&*\/\/:.+="'()\uFF61-\uFFDC\uFFE8-\uFFEE]*$/;
            if (value.length > 0 && regex.test(value)) {
                return true;
            }
            return false;
        }, "半角で{0}文字以内で入力してください。");

        $.validator.addMethod("passwordValidation", function (value) {
            var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;
            if (regex.test(value)) {
                return true;
            }
            return false;
        }, "英数混在の8文字以上で入力してください。");
        $.validator.addMethod("compareDateTimeWithInput", function (value, element, arg) {
            var endDate = $(arg).val();
            if (value == '') {
                return true;
            } else if (typeof endDate == 'undefined' || endDate == '') {
                return true;
            } else {
                return value < endDate;
            }
        }, "Please specify a startDate less than {0}.");
        $.validator.addMethod("compareDateTimeWithOtherTwoInput", function (value, element, arg) {
            if (value == '') {
                return true;
            }
            var startDate = $(arg[0]).val();
            var endDate = $(arg[1]).val();
            if ((typeof startDate == 'undefined' || startDate == '') && (typeof endDate == 'undefined' || endDate == '')) {
                return false;
            } else if (typeof startDate == 'undefined' || startDate == '') {
                return value < endDate;
            } else if (typeof endDate == 'undefined' || endDate == '') {
                return startDate < value;
            } else {
                return (startDate < value && value < endDate);
            }
        }, "Please specify a date between {0} and {1}.");
        $.validator.addMethod("checkFormatDate", function (value, element) {
                return !value || COMMON.isCorrectFormatDate(value);
            },
            "Wrong Format Date"
        );
        $.validator.addMethod("checkFormatDateTime", function (value, element) {
                return !value || COMMON.isCorrectFormatDateTime(value);
            },
            "Wrong Format Date Time");
        //$.validator.addMethod("exportFormatDate", function(value, element) { 
//           var check = false;
//            var re = /^\d{4}\-\d{1,2}$/;
//            console.log(re.test(value))
//            if( re.test(value)){
//                var adata = value.split('-');
//                var yyyy = parseInt(adata[0],10);
//                var mm = parseInt(adata[1],10);
//                
//                var xdata = new Date(yyyy,mm-1,1);
//                if ( ( xdata.getFullYear() == yyyy ) && ( xdata.getMonth () == mm - 1 ) )
//                    check = true;
//                else
//                    check = false;
//            } else
//                check = false;
//            return this.optional(element) || check;
//        }, "Wrong Format Date");    
        
        $.validator.prototype.clean = function (selector) {
            var element = $(selector)[0];
            if (element.tagName === "TEXTAREA" || (element.tagName === "INPUT" && element.type !== "password")) {
                element.value = $.trim(element.value);
            }
            return element;
        }
        $.validator.prototype.focusInvalid = function (selector) {
            if (this.settings.focusInvalid) {
                try {
                    $(this.errorList.length && this.errorList[0].element || [])
                        .filter(":visible")
                        .focus()

                        // Manually trigger focusin event; without it, focusin handler isn't called, findLastActive won't have anything to find
                        .trigger("focusin");
                } catch (e) {

                    // Ignore IE throwing errors when focusing hidden elements
                }
            }
        }
        $.extend($.validator.messages, {
            required: "必須入力項目です。",
            remote: "Please fix this field.",
            email: "メールが半角の256文字以下であり、メールのフォーマットが正しいこと。",
            url: "Please enter a valid URL.",
            date: "Please enter a valid date.",
            dateISO: "Please enter a valid date ( ISO ).",
            number: "Please enter a valid number.",
            digits: "Please enter only digits.",
            equalTo: "Please enter the same value again.",
            maxlength: $.validator.format("Please enter no more than {0} characters."),
            minlength: $.validator.format("Please enter at least {0} characters."),
            rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
            range: $.validator.format("Please enter a value between {0} and {1}."),
            max: $.validator.format("Please enter a value less than or equal to {0}."),
            min: $.validator.format("Please enter a value greater than or equal to {0}."),
            step: $.validator.format("Please enter a multiple of {0}.")
        });
    },
    getFormattedMorthYear: function (date) {
        var _year = date.getFullYear();
        var _month = (1 + date.getMonth()).toString();
        _month = _month.length > 1 ? _month : '0' + _month;
        return _year + '-' + _month;
    },


    getFormattedDate: function (date) {
        var _year = date.getFullYear();
        var _month = (1 + date.getMonth()).toString();
        _month = _month.length > 1 ? _month : '0' + _month;
        var _day = date.getDate().toString();
        _day = _day.length > 1 ? _day : '0' + _day;
        return _year + '-' + _month + '-' + _day;
    },
    getFormattedDateTime: function (date) {
        var _year = date.getFullYear();
        var _month = (1 + date.getMonth()).toString();
        _month = _month.length > 1 ? _month : '0' + _month;
        var _day = date.getDate().toString();
        _day = _day.length > 1 ? _day : '0' + _day;
        var _hour = date.getHours().toString();
        _hour = _hour.length > 1 ? _hour : '0' + _hour;
        var _minute = date.getMinutes().toString();
        _minute = _minute.length > 1 ? _minute : '0' + _minute
        var _second = date.getSeconds().toString();
        _second = _second.length > 1 ? _second : '0' + _second
        return _year + '-' + _month + '-' + _day + ' ' + _hour + ':' + _minute + ':' + _second;
    },
    checkAll: function (button) {
        $('input[name="rowTab1[]"]').prop('checked', button.checked);
    },
    checkAllTab2: function (button) {
        $('input[name="rowTab2[]"]').prop('checked', button.checked);
    },
    checkExistRow: function (msg, url) {
        var n = $('input[name="rowTab1[]"]:checked').length;
        if (n === 0) {
            var MSGCreateGrade = msg;
            popupMessage(MSGCreateGrade, '', 'ID');

        } else {
            location.href = url;
        }
    },
    checkSpecificExistRow: function (msg, url) {
        var n = $('input[name="rowTab1[]"]:checked').length;
        if (n === 1) {
            location.href = url;
        } else {
            var MSGCreateGrade = msg;
            COMMON.popupMessage(MSGCreateGrade);
        }
    },
    confirm_double: function (msg1, title1, msg2, title2, nameOK, nameCancel, url) {
        COMMON.confirm_message(msg1, function () {
            if (typeof url === 'undefined' || url === null)
                COMMON.popupMessage(msg2, '', title2);
            else {
                COMMON.popupMessage(msg2, function () {
                        location.href = url;
                    }
                    , title2);
            }
        }, '', title1, nameOK, nameCancel);
    },
    confirm_password: function (msg) {
        msg = '<div class="row"><label class="col-md-12">新しいパスワード</label> <div class="col-md-12"> <input class="form-control" /></div></div>';
        COMMON.confirm_message(msg, function () {
            COMMON.confirm_message_type('ユーザーIDの。。。をパスワードリセットリセット確認してください');
        }, '', 'パスワードリセット', 'リセット', 'キャンセル');
    },
    confirm_redirctUrl: function (msg, url, title, nameOK, nameCancel) {
        COMMON.confirm_message(msg, function () {
                location.href = url;
            }
            ,
            '', title, nameOK, nameCancel);
    },
    confirm_message: function (msg, fncOKCallBack, fncKOCallBack, title, nameOK, nameCancel, fncClose) {
        if (typeof title === 'undefined' || title === null)
            title = "";
        if (typeof nameOK === 'undefined' || nameOK === null)
            nameOK = "はい";
        if (typeof nameCancel === 'undefined' || nameCancel === null)
            nameCancel = "いいえ";
        var confirmPopup = '<div class="modal fade dialog-position" data-backdrop="static" id="confirmPopupModal" tabindex="-1" role="dialog"';
        confirmPopup += 'aria-labelledby="mySmallModalLabel" aria-hidden="true">';
        confirmPopup += '<div class="modal-dialog modal-sm dialog-position">';
        confirmPopup += '<div class="modal-content">';
        confirmPopup += '<div class="modal-header">';
        confirmPopup += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        confirmPopup += '<h5 class="modal-title">';
        confirmPopup += title;
        confirmPopup += '</h5>';
        confirmPopup += '</div>';
        confirmPopup += '<div class="modal-body">';
        confirmPopup += '<p>' + msg + '</p>';
        confirmPopup += '</div>';
        confirmPopup += '<div class="modal-footer">';
        //confirmPopup += '<button class="btn btn-large-180" id="btnCancelModel">キャンセル</button>';
        confirmPopup += '<button class="btn btn-default" id="btnCancelConfirm">';
        confirmPopup += nameCancel;
        confirmPopup += '</button>';
        confirmPopup += '<button class="btn btn-primary" id="btnAgreeConfirm" >';
        confirmPopup += nameOK;
        confirmPopup += '</button>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        if ($('#confirmPopupModal').length) {
            $('#confirmPopupModal').remove()
        }
        $('body').append(confirmPopup);
        $('#confirmPopupModal').modal('show');
        $('#btnAgreeConfirm').bind('click', function () {
            if (typeof fncOKCallBack === 'function') {
                fncOKCallBack();
            }
            $('#confirmPopupModal').modal('hide');
        });
        $('#btnCancelConfirm').bind('click', function () {
            if (typeof fncKOCallBack === 'function') {
                fncKOCallBack();
            }
            $('#confirmPopupModal').modal('hide');
        });
        $('button.close').bind('click', function () {
            if (typeof fncClose === 'function') {
                fncClose();
            }
            $('#confirmPopupModal').modal('hide');
        });
        $(document).ready(function () {
            setTimeout($.proxy(function () {
                $('#confirmPopupModal button:first').focus();
            }, this), 500);
        });
    },
    confirm_message_type: function (msg, fncOKCallBack, fncKOCallBack, title, nameOK, nameCancel) {
        if (typeof title === 'undefined' || title === null)
            title = "";
        if (typeof nameOK === 'undefined' || nameOK === null)
            nameOK = "はい";
        if (typeof nameCancel === 'undefined' || nameCancel === null)
            nameCancel = "いいえ";
        var confirmPopup = '<div class="modal fade dialog-position" data-backdrop="static" id="confirmPopupModalType" tabindex="-1" role="dialog"';
        confirmPopup += 'aria-labelledby="mySmallModalLabel" aria-hidden="true">';
        confirmPopup += '<div class="modal-dialog modal-sm dialog-position">';
        confirmPopup += '<div class="modal-content">';
        confirmPopup += '<div class="modal-header">';
        confirmPopup += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        confirmPopup += '<h5 class="modal-title">';
        confirmPopup += title;
        confirmPopup += '</h5>';
        confirmPopup += '</div>';
        confirmPopup += '<div class="modal-body">';
        confirmPopup += '<p>' + msg + '</p>';
        confirmPopup += '</div>';
        confirmPopup += '<div class="modal-footer">';
        //confirmPopup += '<button class="btn btn-large-180" id="btnCancelModel">キャンセル</button>';
        confirmPopup += '<div class="row">';
        confirmPopup += '<div class="col-md-6">';
        confirmPopup += '<button class="btn btn-default pull-left" id="btnCancelConfirmType">';
        confirmPopup += nameCancel;
        confirmPopup += '</button>';
        confirmPopup += '</div>';
        confirmPopup += '<div class="col-md-6">';
        confirmPopup += '<button class="btn btn-primary pull-right" id="btnAgreeConfirmType" >';
        confirmPopup += nameOK;
        confirmPopup += '</button>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        confirmPopup += '</div>';
        if ($('#confirmPopupModalType').length) {
            $('#confirmPopupModalType').remove()
        }
        $('body').append(confirmPopup);
        $('#confirmPopupModalType').modal('show');
        $('#btnAgreeConfirmType').bind('click', function () {
            if (typeof fncOKCallBack === 'function') {
                fncOKCallBack();
            }
            $('#confirmPopupModalType').modal('hide');
        });
        $('#btnCancelConfirmType').bind('click', function () {
            if (typeof fncKOCallBack === 'function') {
                fncKOCallBack();
            }
            $('#confirmPopupModalType').modal('hide');
        });
        $(document).ready(function () {
            setTimeout($.proxy(function () {
                $('#confirmPopupModalType button:first').focus();
            }, this), 500);
        });
    },
    popupMessage: function (msg, fncCallBack, title, btnLabel, btnClass, fncClose) {
        if (!btnLabel) btnLabel = '完了';
        if (!btnClass) btnClass = 'btn-red';
        if (typeof msg != 'undefined' && typeof msg != 'string') {
            messages = '<ul>';
            $.each(msg, function (key, value) {
                messages += '<li>' + value.message + '</li>';
            });
            messages += '</ul>';
            msg = messages;
        }
        var errorPopup = '<div class="modal fade dialog-position" id="errorPopupModal" data-backdrop="static" tabindex="-1" role="dialog"';
        errorPopup += 'aria-labelledby="mySmallModalLabel" aria-hidden="true">';
        errorPopup += '<div class="modal-dialog modal-sm ">';
        errorPopup += '<div class="modal-content">';
        if (typeof title != 'undefined') {
            errorPopup += '<div class="modal-header">';
            errorPopup += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
            errorPopup += '<p class="modal-title">' + title + '</p>';
            errorPopup += '</div>';
        }

        errorPopup += '<div class="modal-body">';
        errorPopup += '<p>' + msg + '</p>';
        errorPopup += '</div>';
        errorPopup += '<div class="modal-footer">';
        errorPopup += '<button class="btn btn-primary ' + btnClass + '" id="btnOkModal" >';
        errorPopup += btnLabel;
        errorPopup += '</button>';
        errorPopup += '</div>';
        errorPopup += '</div>';
        errorPopup += '</div>';
        errorPopup += '</div>';
        errorPopup += '</div>';
        if ($('#errorPopupModal').length) {
            $('#errorPopupModal').remove()
        }
        $('body').append(errorPopup);
        $('#errorPopupModal').modal('show');
        $('#btnOkModal').focus();
        $('#btnOkModal').bind('click', function () {
            if (typeof fncCallBack === 'function') {
                fncCallBack();
            }
            $('#errorPopupModal').modal('hide');
        });
        $('button.close').bind('click', function () {
            if (typeof fncClose === 'function') {
                fncClose();
            }
            $('#errorPopupModal').modal('hide');
        });

        $(document).ready(function () {
            setTimeout($.proxy(function () {
                $('#errorPopupModal button:first').focus();
            }, this), 500);
        });

        $(document).ready(function () {
            setTimeout($.proxy(function () {
                $('#errorPopupModal button:first').focus();
            }, this), 500);
        });

    },
    warningPopup : function (msg, fncCallBack) {
        COMMON.popupMessage(msg, fncCallBack, '注意', 'はい');
    },
    errorPopup : function (msg, fncCallBack) {
        COMMON.popupMessage(msg, fncCallBack, 'エラー', 'はい');
    },
    checkAllRow: function (element,type) {
        if(type=='next') {
            $(element).parent().parent().parent().next().find('input').prop('checked', true);
        } else {
            $(element).parent().parent().parent().parent().find('input').prop('checked', true);
        }
    },
    uncheckAllRow: function (element, type) {
        if (type == 'next') $(element).parent().parent().parent().next().find('input').prop('checked', false);
        else
            $(element).parent().parent().parent().parent().find('input').prop('checked', false);
    },

    cancelInfoUser: function () {
        $("#infoUser").modal('show');
        $("#pleaseWaitDialog").modal("hide");
        $("#changePassWord").modal("hide");
    },
    changePassword: function () {
        $("#changePassWord").modal('show');
        $("#infoUser").modal("hide");
    },

    SaveSettingMail: function () {
        $('#settingMail').modal('hide');
        $('#dialogSettingSucceed').modal('show');
    },
    backSettingMail: function () {
        $('#settingMail').modal();
        $('#saveSettingMail').modal('hide');
    },
    textAreaAdjust: function (el, minHeight) {
        el.style.height = "1px";
        var scrollHeight = el.scrollHeight <= minHeight ? minHeight : el.scrollHeight;
        el.style.height = (scrollHeight) + "px";
//                    el.style.cssText = 'height:auto; padding:0';
//                    el.style.cssText = 'height:' + el.scrollHeight + 'px';
    },
    closeAllDialog: function () {
        $('#infoUser, #settingMail, #listNotice').modal('hide');
    },
    isCorrectFormatDate: function (dateString) {
        // First check for the pattern
        var regex_date = /^\d{4}\/\d{2}\/\d{2}$/;
        if (!regex_date.test(dateString)) {
            return false;
        }
        // Parse the date parts to integers
        var parts = dateString.split("/");
        var day = parseInt(parts[2], 10);
        var month = parseInt(parts[1], 10);
        var year = parseInt(parts[0], 10);
        // Check the ranges of month and year
        if (year < 1000 || year > 3000 || month == 0 || month > 12) {
            return false;
        }
        var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        // Adjust for leap years
        if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)) {
            monthLength[1] = 29;
        }
        // Check the range of the day
        return day > 0 && day <= monthLength[month - 1];
    },
    isCorrectFormatDateTime: function (dateTimeString) {
        var partsDateTime = dateTimeString.split(" ");
        if (typeof partsDateTime == 'undefined' || partsDateTime.length != 2) {
            return false;
        }
        var checkDate = COMMON.isCorrectFormatDate(partsDateTime[0]);
        if (!checkDate) {
            return false;
        }
        var regex_date = /^\d{1,2}:\d{1,2}$/;
        if (!regex_date.test(partsDateTime[1])) {
            return false;
        }
        var parts = partsDateTime[1].split(":");
        var hour = parseInt(parts[0], 10);
        var minutes = parseInt(parts[1], 10);
        //var seconds = parseInt(parts[2], 10);
        if (hour > 24) return false;
        if (minutes >= 60) return false;
        //if (seconds >= 60) return false;
        return true;
    },
    openSettingMailModal: function (userId) {
        $.ajax({
            type: "POST",
            url: "/my-page/check-email",
            data: {
                userId:userId
            },
            success: function(response){
                if(response.status){
                    $("#settingMailModal").modal({backdrop:'static'});
                } else {
                    $("#cannotOpenSettingMailModal .message").html(response.message);
                    $("#cannotOpenSettingMailModal").modal({backdrop:'static'});
                }
            },
            error: function(response, statusText, error){}
        });
    },
    setItemToLocalStorage: function (itemId, type, nameStorage) {
        var exist = false;
        if (!sessionStorage.getItem(nameStorage)) {
            sessionStorage.setItem(nameStorage, "[]");
        }
        var list = JSON.parse(sessionStorage.getItem(nameStorage));
        for (var i = 0; i < list.length; i++) {
            if (list[i] == itemId) {
                exist = true;
                break;
            }
        }
        if (type == 'add') {
            if (!exist) {
                list.push(itemId);
            }
        } else {
            var index = list.indexOf(itemId);
            if (index > -1) {
                list.splice(index, 1);
            }
        }
        sessionStorage.setItem(nameStorage, JSON.stringify(list));
    },
    getItemsFromLocalStorage: function (nameStorage) {
        return JSON.parse(sessionStorage.getItem(nameStorage)) ? JSON.parse(sessionStorage.getItem(nameStorage)) : [];
    },
    removeAllItemsFromLocalStorage: function (nameStorage) {
        if (nameStorage == undefined) {
            sessionStorage.clear();
        } else {
            sessionStorage.removeItem(nameStorage);
        }
    },
    checkAllRowTable: function (e, nameStorage) {
        var checked = e.checked;
        $('input[name="rowTab1[]"]').each(function (i, obj) {
            $(obj).prop('checked', checked);
            var typeSet = checked ? 'add' : 'remove';
            COMMON.setItemToLocalStorage($(obj).val(), typeSet, nameStorage);
        });
        var itemsLength = COMMON.getItemsFromLocalStorage(nameStorage).length;
        $('#checkbox-selected-first').html(itemsLength + '件の選択した施策を');
        $('#checkbox-selected-last').html(itemsLength + '件の選択した施策を');
    },
    checkRowTable: function (element) {
        var typeSet = $(element).is(':checked') ? 'add' : 'remove';
        if (typeSet == 'add') {
            // $('#box-policy-' + $(element).val()).addClass('active');
            COMMON.autoCheckedCheckboxAll();
        } else {
            // $('#box-policy-' + $(element).val()).removeClass('active');
            $('#checkbox-all').prop('checked', false)
        }
        COMMON.setItemToLocalStorage($(element).val(), typeSet, NAME_STORAGE);

        var itemsLength = COMMON.getItemsFromLocalStorage(NAME_STORAGE).length;
        $('#checkbox-selected-first').html(itemsLength + '件の選択した施策を');
        $('#checkbox-selected-last').html(itemsLength + '件の選択した施策を');
    },
    setCheckboxFromLocalStorage: function (nameStorage) {
        var items = COMMON.getItemsFromLocalStorage(nameStorage);
        if (items) {
            items.forEach(function (item) {
                $('input[name="rowTab1[]"][value="' + item + '"]').prop('checked', 'checked');
            });
            COMMON.autoCheckedCheckboxAll();
        }
        $('#checkbox-selected-first').html(items.length + '件の選択した施策を');
        $('#checkbox-selected-last').html(items.length + '件の選択した施策を');
    },
    autoCheckedCheckboxAll: function () {
        var numberItem = $('input[name="rowTab1[]"]').length;
        var numberCheckedItem = $('input[name="rowTab1[]"]:checked').length;
        if(numberItem > 0 && numberItem == numberCheckedItem){
            $('input.checkbox-all').prop('checked', 'checked');
        }
    },
    scrollToAnchor: function (aid) {
        var aTag = $("#" + aid);
        $('html,body').animate({scrollTop: aTag.offset().top}, 0);
    },
    isIE9: function () {
        if(navigator.appVersion.indexOf("MSIE 9.") !== -1
        || navigator.appVersion.indexOf("MSIE 7.") !== -1) {
            return true;
        }
    },
    addZero: function(x,n) {
        while (x.toString().length < n) {
            x = "0" + x;
        }
        return x;
    },
    getCurrentMonth: function(){
        var date = new Date();
        var year = this.addZero(date.getFullYear(), 2);
        var month = this.addZero(date.getMonth() + 1, 2);
        
        var currentMonth = year + "-" +  month;
        return currentMonth;
    }
}
