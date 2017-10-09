$.fn.optVisible = function (show) {
    return show ?
        this.filter("span > option").unwrap() :
        this.filter(":not(span > option)").wrap("<span>").parent().hide();
};
var POLICY = {
    validatorPolicy: false,
    messageList: [],
    nameStorage: 'POLICY_ITEMS',
    NOTICE_RECRUITMENT_FORM_TYPE_1: 53,
    NOTICE_RECRUITMENT_FORM_TYPE_2: 54,
    NOTICE_RECRUITMENT_FORM_TYPE_3: 55,
    countRow: 5,
    URL_LIST_POLICY: '/administrator/policy-management/index',
    URL_EDIT_POLICY: '/administrator/policy-management/edit',
    URL_CLONE_POLICY: '/administrator/policy-management/clone',
    URL_ADD_POLICY: '/administrator/policy-management/add',
    URL_SUBMIT_POLICY: '/administrator/policy-management/save',
    URL_SUCCESS_POLICY: '/administrator/policy-management/success',
    URL_DELETE_POLICY: '/administrator/policy-management/delete',
    URL_PUBLIC_POLICY: '/administrator/policy-management/public',
    URL_PRIVATE_POLICY: '/administrator/policy-management/private',
    clearSessionStorageWhenClickFromOtherPage: function () {
        if (typeof document.referrer != 'undefined') {
            var referer = document.referrer;
            if (referer.indexOf('policy-management') < 0 && document.referrer != '') {
                if (performance.navigation.type != 1) {
                    POLICY.removeAllItemsFromLocalStorage();
                }
            }
        }
    },
    displayDepartmentBox: function () {
        $("#ddlDepartmentId option[value!='']").optVisible(false);
        $("#ddlDivisionId option[value!='']").optVisible(false);
        if ($("#ddlBureauId").val()) {
            $("#ddlDepartmentId option[name='bureauId" + $("#ddlBureauId").val() + "']").optVisible(true);
        }
        if ($("#ddlDepartmentId").val()) {
            $("#ddlDivisionId option[name='departmentId" + $("#ddlDepartmentId").val() + "']").optVisible(true);
        }
        $("#ddlBureauId").on("change", function () {
            $("#ddlDepartmentId option[value!='']").prop("selected", false);
            $("#ddlDivisionId option[value!='']").prop("selected", false);

            $("#ddlDepartmentId option[value!='']").optVisible(false);
            $("#ddlDivisionId option[value!='']").optVisible(false);
            $("#ddlDepartmentId option[name='bureauId" + $("#ddlBureauId").val() + "']").optVisible(true);
        });
        $("#ddlDepartmentId").on("change", function () {
            $("#ddlDivisionId option").prop("selected", false);
            $("#ddlDivisionId option[value!='']").optVisible(false);
            $("#ddlDivisionId option[name='departmentId" + $("#ddlDepartmentId").val() + "']").optVisible(true);
        });

    },
    init: function () {
        window.NAME_STORAGE = POLICY.nameStorage;
        POLICY.clearSessionStorageWhenClickFromOtherPage();
        POLICY.setCheckboxFromLocalStorage();
        POLICY.displayDepartmentBox();
        COMMON.autoCheckedCheckboxAll();
        $("#cbAttentionFlag").on("click", function () {
            var checked = $(this).is(':checked');
            if (checked) {
                COMMON.confirm_message(POLICY.messageList['MSG_PM_003_Content_Confirm_Warning'], '', function () {
                    $("#cbAttentionFlag").removeAttr('checked');
                }, POLICY.messageList['MSG_PM_002_Title_Warning'], 'はい', 'いいえ', function () {
                    $("#cbAttentionFlag").removeAttr('checked');
                });
            } else {
                COMMON.confirm_message(POLICY.messageList['MSG_PM_003_Content_Confirm_Warning'], '', function () {
                    $("#cbAttentionFlag").prop("checked", "checked");
                }, POLICY.messageList['MSG_PM_002_Title_Warning'], 'はい', 'いいえ', function () {
                    $("#cbAttentionFlag").prop("checked", "checked");
                });
            }
        });

        $("#table-list-policy > tbody > tr").click(function () {
            $(this).find('input[name="rowTab1[]"]').click();
        });
        $('input[name="rowTab1[]"]').click(function () {
            var typeSet = $(this).is(':checked') ? 'add' : 'remove';
            if (typeSet == 'add') {
                $('#box-policy-' + $(this).val()).addClass('active');
            } else {
                $('#box-policy-' + $(this).val()).removeClass('active');
            }
            POLICY.setItemToLocalStorage($(this).val(), typeSet);
            var itemsLength = POLICY.getItemsFromLocalStorage().length;
            COMMON.autoCheckedCheckboxAll();
            $('#checkbox-selected-first').html(itemsLength + '件の選択した施策を');
            $('#checkbox-selected-last').html(itemsLength + '件の選択した施策を');
        });
        $('#checkbox-all').on('click', function () {
            var checked = this.checked;
            $('input[name="rowTab1[]"]').each(function (i, obj) {
                $(obj).prop('checked', checked);
                var typeSet = checked ? 'add' : 'remove';
                if (typeSet == 'add') {
                    $('#box-policy-' + $(this).val()).addClass('active');
                } else {
                    $('#box-policy-' + $(this).val()).removeClass('active');
                }
                POLICY.setItemToLocalStorage($(obj).val(), typeSet);
            });
            if(checked){
                $('#table-list-policy>tbody>tr').addClass('active');
            }else $('#table-list-policy>tbody>tr').removeClass('active');
            var itemsLength = POLICY.getItemsFromLocalStorage().length;
            $('#checkbox-selected-first').html(itemsLength + '件の選択した施策を');
            $('#checkbox-selected-last').html(itemsLength + '件の選択した施策を');
        });
    },
    setCheckboxAll: function () {
        var itemsLength = POLICY.getItemsFromLocalStorage().length;
        if (itemsLength > 0) {
            if (itemsLength == $('input[name="rowTab1[]"]').length) {
                $('#checkbox-all').prop('checked', 'checked');
            } else {
                $('#checkbox-all').prop('checked', false);
            }
        }
    },
    setItemToLocalStorage: function (itemId, type) {
        var exist = false;
        if (!sessionStorage.getItem(POLICY.nameStorage)) {
            sessionStorage.setItem(POLICY.nameStorage, "[]");
        }
        var list = JSON.parse(sessionStorage.getItem(POLICY.nameStorage));
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
        sessionStorage.setItem(POLICY.nameStorage, JSON.stringify(list));
    },
    getItemsFromLocalStorage: function () {
        return JSON.parse(sessionStorage.getItem(POLICY.nameStorage)) ? JSON.parse(sessionStorage.getItem(POLICY.nameStorage)) : [];
    },
    removeAllItemsFromLocalStorage: function () {
        sessionStorage.removeItem(POLICY.nameStorage);
    },
    setCheckboxFromLocalStorage: function () {
        var items = POLICY.getItemsFromLocalStorage();
        if (items) {
            items.forEach(function (item) {
                $('#checkbox_policy_' + item).prop('checked', 'checked');
            });
        }
        $('#checkbox-selected-first').html(items.length + '件の選択した施策を');
        $('#checkbox-selected-last').html(items.length + '件の選択した施策を');
    },
    initSuccess: function () {
        $('textarea').each(function (i, obj) {
            COMMON.textAreaAdjust(obj, 74);
        });
        $("#form-input-policy").find("input").prop('disabled', true);
        $("#form-input-policy").find("select").prop('disabled', true);
        $("#form-input-policy").find("textarea").prop('disabled', true);
    },
    initAdd: function () {
        POLICY.displayDepartmentBox();
        $('#txtSummaryUpdate').keyup(function () {
            $('#cbEmailNotificationFlag').prop('checked', 'checked');
            $('#update-date-display-box').show();
            $('#update-date-display-note-box').show();
        });
        $('textarea').each(function (i, obj) {
            COMMON.textAreaAdjust(obj, 74);
        });
        $('.inputTime').datetimepicker({
            format: 'yyyy/mm/dd',
            weekStart: true,
            todayBtn: true,
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
            startView: 2,
            minView: 2,
            forceParse: false,
        });
        $('.publishDate').datetimepicker({
            format: 'yyyy/mm/dd hh:ii',
            forceParse: false,
            todayBtn: true,
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
            weekStart: true
        });
        $('.updateDate').datetimepicker({
            format: 'yyyy/mm/dd hh:ii',
            forceParse: false,
            todayBtn: true,
            clearBtn: true,
            autoclose: true,
            todayHighlight: true,
            weekStart: true
        });
        $.validator.addMethod("requiredDepartment", function (value, element) {
            if (value == '' || value <= 0) {
                return false;
            } else {
                return true;
            }
        }, POLICY.messageList['MSG_PO_001_Required_Field']);
        $.validator.addMethod("requiredStartDate", function (value, element) {
            var endDate = $('#endDate_' + $(element).parent().parent().find('.index').html()).val();
            var deadline = $('#deadline_' + $(element).parent().parent().find('.index').html()).val();
            if ($('#cbRecruitmentFlag').is(':checked') || $('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_3).is(':checked')) {
                return true;
            }
            if ($('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_2).is(':checked')) {
                if (endDate && !value) {
                    return false;
                }
            } else {
                if ((endDate || deadline) && !value) {
                    return false;
                }
            }
            return true;
        }, POLICY.messageList['MSG_PO_001_Required_Field']);
        $.validator.addMethod("compareStartDateWithInput", function (value, element) {
            var endDate = $('#endDate_' + $(element).parent().parent().find('.index').html()).val();
            var deadline = $('#deadline_' + $(element).parent().parent().find('.index').html()).val();
            if ($('#cbRecruitmentFlag').is(':checked') || $('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_3).is(':checked')) {
                return true;
            }
            if ($('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_2).is(':checked')) {
                if ((endDate && value > endDate)) {
                    return false;
                }
            } else {
                if (deadline && value > deadline) {
                    return false;
                } else if (endDate && !deadline && value > endDate) {
                    return false;
                }
            }
            return true;

        }, function (params, element) {
            var value = $(element).val();
            var endDate = $('#endDate_' + $(element).parent().parent().find('.index').html()).val();
            var deadline = $('#deadline_' + $(element).parent().parent().find('.index').html()).val();
            var msg = 'default error';
            if ($('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_2).is(':checked')) {
                if ((endDate && value > endDate)) {
                    msg = POLICY.messageList['MSG_PO_020_Error_Compare_StartDate_With_EndDate'];
                }
            } else {
                if (deadline && value > deadline) {
                    msg = POLICY.messageList['MSG_PO_021_Error_Compare_StartDate_With_Deadline'];
                } else if (endDate && !deadline && value > endDate) {
                    msg = POLICY.messageList['MSG_PO_020_Error_Compare_StartDate_With_EndDate'];
                }
            }
            return msg;
        });
        $.validator.addMethod("compareStartDateWithPublishDate", function (value, element) {
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
            startPublishDate = startPublishDate.replace(' 00:00:00', '');
            endPublishDate = endPublishDate.replace(' 00:00:00', '');
            startPublishDate = startPublishDate.replace(' 00:00', '');
            endPublishDate = endPublishDate.replace(' 00:00', '');
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
        }, function (params, element) {
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
            var msg = 'default error';
            if ($(element).val()) {
                var value = $(element).val();
                if (startPublishDate && endPublishDate) {
                    if (value < startPublishDate || value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_024_Error_Compare_StartDate_With_PublishDate'];
                    }
                } else if (startPublishDate) {
                    if (value < startPublishDate) {
                        msg = POLICY.messageList['MSG_PO_022_Error_Compare_StartDate_With_StartPublishDate'];
                    }
                } else if (endPublishDate) {
                    if (value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_023_Error_Compare_StartDate_With_EndPublishDate'];
                    }
                }
            }
            return msg;
        });

        $.validator.addMethod("requiredEndDate", function (value, element) {
            var startDate = $('#startDate_' + $(element).parent().parent().find('.index').html()).val();
            var deadline = $('#deadline_' + $(element).parent().parent().find('.index').html()).val();
            if ($('#cbRecruitmentFlag').is(':checked') || $('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_3).is(':checked')) {
                return true;
            }
            if ($('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_2).is(':checked')) {
                if (startDate && !value) {
                    return false;
                }
            } else {
                if (startDate && !value && !deadline) {
                    return false;
                }
            }
            return true;
        }, POLICY.messageList['MSG_PO_001_Required_Field']);
        $.validator.addMethod("compareEndDateWithPublishDate", function (value, element) {
            var deadline = $('#deadline_' + $(element).parent().parent().find('.index').html()).val();
            if ($('#cbRecruitmentFlag').is(':checked') || $('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_3).is(':checked')) {
                return true;
            }
            if (!$('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_2).is(':checked')) {
                if (deadline) {
                    return true;
                }
            }
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
            startPublishDate = startPublishDate.replace(' 00:00:00', '');
            endPublishDate = endPublishDate.replace(' 00:00:00', '');
            startPublishDate = startPublishDate.replace(' 00:00', '');
            endPublishDate = endPublishDate.replace(' 00:00', '');
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
        }, function (params, element) {
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
            var msg = 'default error';
            if ($(element).val()) {
                var value = $(element).val();
                if (startPublishDate && endPublishDate) {
                    if (value < startPublishDate || value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_027_Error_Compare_EndDate_With_PublishDate'];
                    }
                } else if (startPublishDate) {
                    if (value < startPublishDate) {
                        msg = POLICY.messageList['MSG_PO_025_Error_Compare_EndDate_With_StartPublishDate'];
                    }
                } else if (endPublishDate) {
                    if (value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_026_Error_Compare_EndDate_With_EndPublishDate'];
                    }
                }
            }
            return msg;
        });

        $.validator.addMethod("requiredDeadline", function (value, element) {
            var startDate = $('#startDate_' + $(element).parent().parent().find('.index').html()).val();
            var endDate = $('#endDate_' + $(element).parent().parent().find('.index').html()).val();
            if ($('#cbRecruitmentFlag').is(':checked') || $('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_3).is(':checked')) {
                return true;
            }
            if (!$('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_2).is(':checked')) {
                if (startDate && !endDate && !value) {
                    return false;
                }
            }
            return true;
        }, POLICY.messageList['MSG_PO_001_Required_Field']);
        $.validator.addMethod("compareDeadlineWithPublishDate", function (value, element) {
            if ($('#cbRecruitmentFlag').is(':checked') || $('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_3).is(':checked')) {
                return true;
            }
            if ($('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_2).is(':checked')) {
                return true;
            }
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
            startPublishDate = startPublishDate.replace(' 00:00:00', '');
            endPublishDate = endPublishDate.replace(' 00:00:00', '');
            startPublishDate = startPublishDate.replace(' 00:00', '');
            endPublishDate = endPublishDate.replace(' 00:00', '');
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
        }, function (params, element) {
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
            var msg = 'default error';
            if ($(element).val()) {
                var value = $(element).val();
                if (startPublishDate && endPublishDate) {
                    if (value < startPublishDate || value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_030_Error_Compare_Deadline_With_PublishDate'];
                    }
                } else if (startPublishDate) {
                    if (value < startPublishDate) {
                        msg = POLICY.messageList['MSG_PO_028_Error_Compare_Deadline_With_StartPublishDate'];
                    }
                } else if (endPublishDate) {
                    if (value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_029_Error_Compare_Deadline_With_EndPublishDate'];
                    }
                }
            }
            return msg;
        });

        $.validator.addMethod("comparePublishDate", function (value, element) {
            var endPublishDate = $('#datePublishEnddate').val();
            if (endPublishDate && value) {
                if (value > endPublishDate) {
                    return false;
                }
            }
            return true;
        }, POLICY.messageList['MSG_PO_006_Error_Publish_Start_Greater_End']);
        $.validator.addMethod("compareUpdateDateWithPublishDate", function (value, element) {
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
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
        }, function (params, element) {
            var startPublishDate = $('#datePublishStartdate').val();
            var endPublishDate = $('#datePublishEnddate').val();
            var msg = 'default error';
            if ($(element).val()) {
                var value = $(element).val();
                if (startPublishDate && endPublishDate) {
                    if (value < startPublishDate || value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_033_Error_Compare_UpdateDate_With_PublishDate'];
                    }
                } else if (startPublishDate) {
                    if (value < startPublishDate) {
                        msg = POLICY.messageList['MSG_PO_031_Error_Compare_UpdateDate_With_StartPublishDate'];
                    }
                } else if (endPublishDate) {
                    if (value > endPublishDate) {
                        msg = POLICY.messageList['MSG_PO_032_Error_Compare_UpdateDate_With_EndPublishDate'];
                    }
                }
            }
            return msg;
        });
        $.validator.addMethod("checkFormatDate", function (value, element) {
            return !value || COMMON.isCorrectFormatDate(value);
        }, POLICY.messageList['MSG_PO_002_Error_Format_Date']);
        $.validator.addMethod("checkFormatDateTime", function (value, element) {
            return !value || COMMON.isCorrectFormatDateTime(value);
        }, POLICY.messageList['MSG_PO_005_Error_Publish_Date_Format']);

        POLICY.validatorPolicy = $("#form-input-policy").validate({
            ignore: ':hidden:not(.attributes)',
            rules: {
                ddlBureauId: {
                    requiredDepartment: true
                },
                ddlDepartmentId: {
                    //requiredDepartment: true
                },
                ddlDivisionId: {
                    //requiredDepartment: true
                },
                txtShortName: {
                    required: true,
                    checkFullSizeWithLength: 20
                },
                txtName: {
                    required: true,
                    checkFullSizeWithLength: 50
                },
                txtPurpose: {
                    required: true,
                    checkFullSizeWithLength: 1000
                },
                txtDetailOfSupportArea: {
                    required: true,
                    checkFullSizeWithLength: 1000
                },
                txtContent: {
                    required: true,
                    checkFullSizeWithLength: 2000
                },
                txtDetailRecruitmentTime: {
                    required: true,
                    checkFullSizeWithLength: 1000
                },
                txtHomepage: {
                    required: true,
                    checkHalfSizeWithLength: 100
                },
                txtContact: {
                    required: true,
                    checkFullSizeWithLength: 500
                },
                datePublishStartdate: {
                    checkFormatDateTime: true,
                    comparePublishDate: true
                },
                datePublishEnddate: {
                    checkFormatDateTime: true
                },
                txtSummaryUpdate: {
                    required: true,
                    checkFullSizeWithLength: 1000
                },
                updateDateDisplay: {
                    required: true,
                    checkFormatDate: true
                },
                updateDate: {
                    checkFormatDateTime: true,
                    compareUpdateDateWithPublishDate: true
                }
            },
            highlight: function (element, errorClass, validClass) {
                if (element.type === "radio" || element.type === "checkbox") {
                    this.findByName(element.name).addClass(errorClass).removeClass(validClass);
                } else {
                    $(element).addClass(errorClass).removeClass(validClass);
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                if (element.type === "radio" || element.type === "checkbox") {
                    this.findByName(element.name).removeClass(errorClass).addClass(validClass);
                } else {
                    $(element).removeClass(errorClass).addClass(validClass);
                }
            },
            errorPlacement: function (error, element) {
                if (element.hasClass('attributes')) {
                    if (element.attr('data-title') == 'attributesSelect') {
                        error.insertAfter(element);
                    } else if (element.attr('type') == 'radio') {
                        error.appendTo('.error-radio');
                    } else {
                        error.insertAfter(element.parent().parent().parent());
                    }
                } else if (element.hasClass('startDate') || element.hasClass('endDate') || element.hasClass('deadline')) {
                    $('.recruitment-time-item').css('margin-bottom', '0');
                    $('#error-recruitment-time-' + element.attr('data-id')).show();
                    $('#error_' + element.attr('id')).html('');
                    error.appendTo('#error_' + element.attr('id'));
                } else if (element.parent().hasClass('publishStartDate')) {
                    $('#pendingUpdate').css('margin-bottom', '0');
                    error.appendTo('#error-publish-start-date');
                } else if (element.parent().hasClass('publishEndDate')) {
                    $('#pendingUpdate').css('margin-bottom', '0');
                    error.appendTo('#error-publish-end-date');
                } else if (element.parent().hasClass('updateDate')) {
                    $('#update-date-box').css('margin-bottom', '0');
                    error.appendTo('#error-update-date');
                } else if (element.parent().hasClass('updateDateDisplay')) {
                    $('#update-date-display-box').css('margin-bottom', '0');
                    error.appendTo('#error-update-display-date');
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form, event) {
                if ($(this.submitButton).attr('id') == 'btnDraft') {
                    var input = $("<input>").attr("type", "hidden").attr("name", "isDraft").val(1);
                    $(form).append($(input));
                } else {
                    var input = $("<input>").attr("type", "hidden").attr("name", "isDraft").val(0);
                    $(form).append($(input));
                }
                // do other things for a valid form
                event.preventDefault();
                COMMON.confirm_message(POLICY.messageList['MSG_PM_005_Content_Confirm_Save_Policy'], function () {
                    POLICY.submitPolicyIE(form);
                }, '', POLICY.messageList['MSG_PM_004_Title_Confirm_Save_Policy'], '確認', 'キャンセル');
                return false;
            },
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    if ($(validator.errorList[0].element).is(':hidden')) {
                        var elementFocus = $(validator.errorList[0].element).parent().parent();
                        $(elementFocus).attr("tabindex", -1).focus();
                    } else {
                        var elementFocus = $(validator.errorList[0].element);
                        $(elementFocus).focus();
                    }

                }
            }
        });
        POLICY.setIndex();
        POLICY.setAttributesRule();
        POLICY.checkRecruitmentTime();
    },
    setAttributesRule: function () {
        var attributeNotRequired = [7, 8];
        $('.box-attributes').each(function (i, obj) {
            var attributeType = parseInt($(obj).attr('data-id'));
            if (attributeNotRequired.indexOf(attributeType) < 0) {
                if ($(obj).children().attr('data-title') == 'attributesSelect') {
                    $('select[name="attributesSelect[' + attributeType + ']"]').rules("add", {
                        required: true
                    });
                } else {
                    $('input[name="attributes[' + attributeType + '][]"]').rules("add", {
                        required: true
                    });
                }
            }
        })
    },
    setIndex: function () {
        $("#box-recruitment-time .recruitment-time-item").each(function (i, obj) {
            var itemId = 'recruitment-time-item-' + (i + 1);
            $(this).attr('id', itemId);
            $(this).find('.add').attr('data-id', (i + 1))
            $(this).find('.remove').attr('data-id', (i + 1))
            $(obj).next().attr('id', 'error-recruitment-time-' + (i + 1));
            $(obj).next().find('.input-group').first().attr('id', 'error_startDate_' + (i + 1));
            $(obj).next().find('.input-group').first().next().attr('id', 'error_endDate_' + (i + 1));
            $(obj).next().find('.input-group').last().attr('id', 'error_deadline_' + (i + 1));
            $(obj).find('input.startDate').attr('data-id', (i + 1));
            $(obj).find('input.endDate').attr('data-id', (i + 1));
            $(obj).find('input.deadline').attr('data-id', (i + 1));
            if ($(obj).find(".index").length > 0) {
                $(obj).find(".index").html(i + 1);
            }
            if ($(obj).find(".startDate").length > 0) {
                $(obj).find(".startDate").attr('id', 'startDate_' + (i + 1));
                $(obj).find(".startDate").attr('name', 'startDate[' + (i + 1) + ']');
                $(obj).find(".startDate").rules("add", {
                    requiredStartDate: true,
                    checkFormatDate: true,
                    compareStartDateWithInput: true,
                    compareStartDateWithPublishDate: true
                });
            }
            if ($(obj).find(".endDate").length > 0) {
                $(obj).find(".endDate").attr('id', 'endDate_' + (i + 1));
                $(obj).find(".endDate").attr('name', 'endDate[' + (i + 1) + ']');
                $(obj).find(".endDate").rules("add", {
                    requiredEndDate: true,
                    checkFormatDate: true,
                    compareEndDateWithPublishDate: true
                });
            }
            if ($(obj).find(".deadline").length > 0) {
                $(obj).find(".deadline").attr('id', 'deadline_' + (i + 1));
                $(obj).find(".deadline").attr('name', 'deadline[' + (i + 1) + ']');
                $(obj).find(".deadline").rules("add", {
                    requiredDeadline: true,
                    checkFormatDate: true,
                    compareDeadlineWithPublishDate: true
                });
            }
        });
    },
    searchPolicy: function () {
        POLICY.removeAllItemsFromLocalStorage();
        $('#search-policy').submit();
    },
    refeshPage: function () {
        window.location.reload();
    },
    redirectToUrl: function (url) {
        POLICY.removeAllItemsFromLocalStorage();
        if (typeof url == 'undefined' || url == '' || !url) {
            window.location = POLICY.URL_LIST_POLICY;
        } else {
            window.location = url;
        }
        return;
    },
    redirectToListPolicy: function () {
        window.location = POLICY.URL_LIST_POLICY;
        return;
    },
    redirectToAddPolicy: function () {
        window.location = POLICY.URL_ADD_POLICY;
        return;
    },
    redirectToEditPolicy: function (policyId, linkEdit) {
        if ($('#checkbox_policy_' + policyId).is(':checked')) {
            $('#checkbox_policy_' + policyId).prop('checked', false);
        } else {
            $('#checkbox_policy_' + policyId).prop('checked', 'checked');
        }
        window.location = linkEdit;
        return;
    },
    submitPolicyIE: function (form) {
        $(form).ajaxSubmit({
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    window.location = POLICY.URL_SUCCESS_POLICY + '/' + data.policyId;
                } else {
                    var errorContent = "<ul>";
                    $.each(data.errors, function (key) {
                        errorContent += "<li>" + this + "</li>"
                    });
                    errorContent += "</ul>";
                    $("#errors-list").html(errorContent);
                    $('html, body').animate({scrollTop: $('.container-body').offset().top}, 'slow');
                }
            },
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#btnDraft').attr('disabled', 'disabled');
                $('#btnSubmit').attr('disabled', 'disabled');
            },
            complete: function () {
                $('#btnDraft').removeAttr('disabled');
                $('#btnSubmit').removeAttr('disabled');
            },
            error: function () {

            }
        });
    },
    submitPolicy: function submit(form) {
        var formData = new FormData($(form)[0]);
        $.ajax({
                type: 'POST',
                url: POLICY.URL_SUBMIT_POLICY,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        window.location = POLICY.URL_SUCCESS_POLICY + '/' + data.policyId;
                    } else {
                        var errorContent = "<ul>";
                        $.each(data.errors, function (key) {
                            errorContent += "<li>" + this + "</li>"
                        });
                        errorContent += "</ul>";
                        $("#errors-list").html(errorContent);
                        $('html, body').animate({scrollTop: $('.container-body').offset().top}, 'slow');
                    }
                },
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#btnDraft').attr('disabled', 'disabled');
                    $('#btnSubmit').attr('disabled', 'disabled');
                },
                complete: function () {
                    $('#btnDraft').removeAttr('disabled');
                    $('#btnSubmit').removeAttr('disabled');
                },
                error: function (err) {

                }
            }
        );
    },
    checkRecruitmentTime: function () {
        if ($('input[name="cbRecruitmentFlag"]').is(':checked') || $('#attributes_' + POLICY.NOTICE_RECRUITMENT_FORM_TYPE_3).is(':checked')) {
            $('#box-recruitment-time').hide();
            $('#box-recruitment-time').find('input').attr('disabled', 'disabled');
            $('.startDate').removeClass('error');
            $('.endDate').removeClass('error');
            $('.deadline').removeClass('error');
            $('.recruitment-time-item label.error').remove();
            $('#box-recruitment-time').find('label.error').remove();
            $('#txtDetailRecruitmentTime').rules('remove', 'required');
            $('#txtDetailRecruitmentTime').removeClass('error');
            $('label#txtDetailRecruitmentTime-error').remove();
        } else {
            $('#box-recruitment-time').show();
            $('#box-recruitment-time').find('input').removeAttr('disabled');
            $('#txtDetailRecruitmentTime').rules('add', {
                required: true,
                checkFullSizeWithLength: 1000
            });
        }
    },

    addNewRow: function (item) {
        var index = '';
        var stringHtml = '<div class="form-group recruitment-time-item">' +
                '<label for="inputPassword3" class="col-sm-1 col-xs-1 col-md-offset-1 col-sm-offset-1 text-right"><h5 class="index">' + index + '</h5></label>' +
                '<div class="start-group btn-space col-sm-3 col-xs-12 input-group date inputTime">' +
                '<input readonly="true" type="text" id="startDate_' + index + '" name="startDate[]" class="form-control inputTimeNew startDate"/>' +
                '<span class="input-group-addon">' +
                '<span class="glyphicon glyphicon-calendar"></span>' +
                '</span>' +
                '</div>' +
                '<div class="end-group btn-space col-sm-3 col-xs-12 input-group date inputTime">' +
                '<input readonly="true" type="text" id="endDate_' + index + '" name="endDate[]" class="form-control inputTimeNew endDate"/>' +
                '<span class="input-group-addon">' +
                '<span class="glyphicon glyphicon-calendar"></span>' +
                '</span>' +
                '</div>' +
                '<div  class="deadline-group btn-space col-sm-3 col-xs-12 input-group date inputTime">' +
                '<input readonly="true" type="text" id="deadline_' + index + '" name="deadline[]" class="form-control inputTimeNew deadline"/>' +
                '<span class="input-group-addon">' +
                '<span class="glyphicon glyphicon-calendar"></span>' +
                '</span>' +
                '</div>' +
                '<div class="col-sm-1 col-xs-1">' +
                '<h5>' +
                '<a class="add" onclick="POLICY.addNewRow(this)" style="cursor: pointer;"><span class="col-md-1 glyphicon glyphicon-plus-sign"></span></a>' +
                '<a class="remove" onclick="POLICY.removeRow(this)" style="cursor: pointer;"><span class="col-md-1 glyphicon glyphicon-minus-sign"></span></a>' +
                '</h5>' +
                '</div>' +
                '</div>' +
                '<div class="form-group" style="display: none;">' +
                '<label for="inputPassword3" class="col-sm-1 col-xs-1 col-md-offset-1 col-sm-offset-1 text-right">' +
                '</label>' +
                '<div class="col-sm-3 col-xs-12 input-group start-group">&nbsp;</div>' +
                '<div class="col-sm-3 col-xs-12 input-group end-group">&nbsp;</div>' +
                '<div class="col-sm-3 col-xs-12 input-group deadline-group">&nbsp;</div>' +
                '</div>'
            ;
        if ($("#box-recruitment-time .recruitment-time-item").size() < 20) {
            var key = $(item).attr('data-id');
            $('#error-recruitment-time-' + key).after(stringHtml);
            //$('#box-recruitment-time').append(stringHtml);
            $('.inputTime').datetimepicker({
                format: 'yyyy/mm/dd',
                weekStart: true,
                todayBtn: true,
                clearBtn: true,
                autoclose: true,
                todayHighlight: true,
                startView: 2,
                minView: 2,
                forceParse: false,
            });
            POLICY.setIndex();
        } else {
            var key = $(item).attr('data-id');
            $('#recruitment-time-item-' + key).find("input").val('');
        }
        if ($("#box-recruitment-time .recruitment-time-item").size() >= 20) {
            $(".add span").removeClass('glyphicon-plus-sign').addClass('glyphicon-pencil');
        }
    },
    removeRow: function (item) {
        if ($("#box-recruitment-time .recruitment-time-item").size() > 1) {
            COMMON.confirm_message('データを削除しますか。', function () {
                var key = $(item).attr('data-id');
                $('#recruitment-time-item-' + key).remove();
                $('#error-recruitment-time-' + key).remove();
                POLICY.setIndex();
                if ($("#box-recruitment-time .recruitment-time-item").size() == 1) {
                    $(".remove").addClass("disabled");
                } else {
                    $(".remove").removeClass("disabled");
                }
                $(".add span").removeClass('glyphicon-pencil').addClass('glyphicon-plus-sign');
            }, '', POLICY.messageList['MSG_PM_0015_Title_Delete_Row']);
        } else {
            $(item).parent().parent().parent().find("input").val('');
        }
    },

    checkUploadFile: function () {
        $('#modalUploadMessage').modal();
        $('#modalUpload').modal('hide');
    },
    backUpload: function () {
        $('#modalUpload').modal();
        $('#modalUploadMessage').modal('hide');
    },
    save: function () {
        $('#modalUploadConfirm').modal();
        $('#modalUploadMessage').modal('hide');
    },
    clonePolicy: function () {
        var items = POLICY.getItemsFromLocalStorage();
        if (items.length == 1) {
            location.href = POLICY.URL_CLONE_POLICY + '/' + items[0];
        } else if (items.length == 0) {
            var MSGCreateGrade = POLICY.messageList['MSG_PM_001_EmptyPolicyIds'];
            COMMON.warningPopup(MSGCreateGrade, '');
        }
        else {
            var MSGCreateGrade = '1つのみチェックを入れてください。';
            COMMON.warningPopup(MSGCreateGrade, '');
        }
    },
    privatePolicy: function () {
        var items = POLICY.getItemsFromLocalStorage();
        var itemsLength = items.length;
        if (itemsLength < 1) {
            COMMON.warningPopup(POLICY.messageList['MSG_PM_001_EmptyPolicyIds'], '');
            return false;
        }
        COMMON.confirm_message(POLICY.messageList['MSG_PM_007_Content_Private_Policy'], function () {
            $.ajax({
                    type: 'POST',
                    url: POLICY.URL_PRIVATE_POLICY,
                    data: {policyIds: items},
                    dataType: 'json',
                    success: function (data) {
                        POLICY.removeAllItemsFromLocalStorage();
                        COMMON.popupMessage(data.message, function () {
                            POLICY.refeshPage()
                        }, POLICY.messageList['MSG_PM_0012_Title_Private_Policy_Succeed'], '', '', function () {
                            POLICY.refeshPage()
                        });
                    },
                    beforeSend: function () {

                        $('#btnPrivate').attr('disabled', 'disabled');
                    },
                    complete: function () {
                        $('#btnPrivate').removeAttr('disabled');
                    },
                    error: function (err) {

                    }
                }
            );
        }, '', POLICY.messageList['MSG_PM_006_Title_Private_Policy']);
    },
    publicPolicy: function () {
        var items = POLICY.getItemsFromLocalStorage();
        var itemsLength = items.length;
        if (itemsLength < 1) {
            COMMON.warningPopup(POLICY.messageList['MSG_PM_001_EmptyPolicyIds'], '');
            return false;
        }
        COMMON.confirm_message(POLICY.messageList['MSG_PM_009_Content_Public_Policy'], function () {
            $.ajax({
                    type: 'POST',
                    url: POLICY.URL_PUBLIC_POLICY,
                    data: {policyIds: items},
                    dataType: 'json',
                    success: function (data) {
                        POLICY.removeAllItemsFromLocalStorage();
                        COMMON.popupMessage(data.message, function () {
                            POLICY.refeshPage()
                        }, POLICY.messageList['MSG_PM_0013_Title_Public_Policy_Succeed'], '', '', function () {
                            POLICY.refeshPage()
                        });
                    },
                    beforeSend: function () {
                        $('#btnPublic').attr('disabled', 'disabled');
                    },
                    complete: function () {
                        $('#btnPublic').removeAttr('disabled');
                    },
                    error: function (err) {

                    }
                }
            );
        }, '', POLICY.messageList['MSG_PM_008_Title_Public_Policy']);
    },
    deletePolicy: function () {
        var items = POLICY.getItemsFromLocalStorage();
        var itemsLength = items.length;
        if (itemsLength < 1) {
            COMMON.warningPopup(POLICY.messageList['MSG_PM_001_EmptyPolicyIds'], '');
            return false;
        }
        COMMON.confirm_message(POLICY.messageList['MSG_PM_0011_Content_Delete_Policy'], function () {
            $.ajax({
                    type: 'POST',
                    url: POLICY.URL_DELETE_POLICY,
                    data: {policyIds: items},
                    dataType: 'json',
                    success: function (data) {
                        POLICY.removeAllItemsFromLocalStorage();
                        COMMON.popupMessage(data.message, function () {
                            POLICY.refeshPage()
                        }, POLICY.messageList['MSG_PM_0014_Title_Delete_Policy_Succeed'], '', '', function () {
                            POLICY.refeshPage()
                        });
                    },
                    beforeSend: function () {

                        $('#btnDelete').attr('disabled', 'disabled');
                    },
                    complete: function () {
                        $('#btnDelete').removeAttr('disabled');
                    },
                    error: function (err) {

                    }
                }
            );
        }, '', POLICY.messageList['MSG_PM_0010_Title_Delete_Policy']);
    },
    showTextPolicy: function (policyId) {
        if ($('#name-' + policyId).hasClass('short-text')) {
            $('#name-' + policyId).html($('#fullText_' + policyId).val()).removeClass('short-text').addClass('full-text');
            $('#add-more-' + policyId).remove();
        } else {
            $('#name-' + policyId).html($('#shortText_' + policyId).val()).removeClass('full-text').addClass('short-text');
        }
        return;
    }
}
