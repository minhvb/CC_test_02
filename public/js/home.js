/**
 * Created by FPT Software on 08/02/2017.
 */

var HOME = {
    PREFIX_NAME_STORAGE: 'home_screen_',// this field will be changed when load page
    NAME_STORAGE: '',// this field will be changed when load page
    URL_FAVOURITE_POLICY: 'home/add-favourite',
    URL_REMOVE_FAVOURITE_POLICY: 'home/remove-favourite',
    URL_ADD_SEARCH_HISTORY: 'home/add-search-history',
    URL_LOAD_SEARCH_HISTORY: 'home/load-search-history',
    addPolicyValidator: null,
    init: function (FILTER) {
        HOME.NAME_STORAGE = HOME.PREFIX_NAME_STORAGE + FILTER;
        window.NAME_STORAGE = HOME.NAME_STORAGE;
        HOME.clearSessionStorageWhenClickFromOtherPage(FILTER);
        COMMON.setCheckboxFromLocalStorage(HOME.NAME_STORAGE);
        $('#btnPopupLoadSearch').click(function () {
            HOME.showPopupLoadHistory();
        });

        $('#saveCondition').click(function () {
            HOME.showPopupAddHistory();
        });

        $('#historySave').on('hidden.bs.modal', function () {
            HOME.clearError($("#history-form"), HOME.addPolicyValidator);
        });

        $('#searchConditionCall').on('hidden.bs.modal', function () {
            HOME.clearError($("#form-select-history"), HOME.loadHistoryValidator);
        });

        HOME.addPolicyValidator = $("#history-form").validate({
            rules:{
                historyName: {
                    required: true,
                    checkFullSizeWithLength: 50
                }
            },
            submitHandler: function(form, event) {
                // do other things for a valid form
                HOME.addSearchHistory();
                event.preventDefault ? event.preventDefault() : (event.returnValue = false);
                return false;
            }
        });
        $('.exportCompare').click(function () {
            HOME.exportCompare();
        });

        HOME.loadHistoryValidator = $("#form-select-history").validate({
            rules:{
                // historyId: "required",
            },
            submitHandler: function(form, event) {
                // do other things for a valid form
                HOME.loadHistory();
                event.preventDefault ? event.preventDefault() : (event.returnValue = false);
                return false;
            }
        });
        $(document).ready(function () {
            $("#carouselOne").carousel({
                interval: 5000
            });
            $("#table1 > tbody > tr:not(.disableCheck)").click(function () {
                $(this).find('input[name="rowTab1[]"]').click();
            });
            $('input[name="rowTab1[]"]').click(function () {
                $(this).click();
            });
            $("#detailSearch").hide()

            $("#btnPopupHistory").click(function () {
                $("#searchConditionCall").modal('show');
            })

        });
        function onActive(item) {
            $(".menu-sort li").removeClass('active');
            $(item).addClass('active');
        }

        //doan nay den nua refactor  thanh function common
        $("#boxSearch1").click(function () {
            $("#saveCondition").show();
            $("#detailSearch1").toggle();
            $("#box-search-2").toggle();
            $('#boxSearch1').toggleClass('open');
            $('#boxSearch2').removeClass('open');

            if ($('#detailSearch1:visible').length == 0) {
                $("#saveCondition").hide();
                $('#btnClearAll').hide();
                $('#btn-search-second').hide();
                $('#searchBoxStatus').val('');
            }

            if ($('#detailSearch1:visible').length == 1) {
                $("#saveCondition").show();
                $('#btnClearAll').show();
                $('#btn-search-second').show();
                $('#searchBoxStatus').val('box1');
            }
            if ($('#detailSearch2:visible').length == 1) {
                $('#detailSearch2').toggle();
                $('#detail-search-block').css('display', 'none');

            }
        });
        $("#boxSearch2").click(function () {
            $("#saveCondition").show();
            $("#detailSearch2").toggle();
            $('#boxSearch2').toggleClass('open');
            if ($('#detailSearch2:visible').length == 0) {
                $('#searchBoxStatus').val('box1');
                $('#detail-search-block').css('display', 'none');
            }

            if ($('#detailSearch2:visible').length == 1) {
                $('#searchBoxStatus').val('box2');
                $('#detail-search-block').css('display', 'block');
            }
        });
        function onMailSettingOpition() {
            $("#settingMail").modal();
            $("#btnSaveSettingMail").text('保存');
            $("#settingMail").find("input").prop("disabled", true);
            $("#settingMail").find("input[name='checked']").prop("checked", true);
            $("#settingMail").find("button[name='selectAll']").hide();
            $("#settingMail").find("button[name='unSelectAll']").hide();
            $("#settingMail").find("select").prop("disabled", true);
        }

        function showSaveOption() {
            var checkedIDs = ["id-C1", "id-C3", "id-C5", "id-A1", "id-A4", "id-B1", "id-B5", "id-D3", "id-E5", "id-F2"];
            $("#saveCondition").show();
            $("#detailSearch1").toggle();
            $("#boxSearch2").toggle();
            $("#detailSearch2").toggle();
            $.each(checkedIDs, function (index, value) {
                $("#" + value).prop("checked", true);
            });
        }
    },
    initBoxSearch: function () {
        if($('#searchBoxStatus').val() == 'box1'){
            $("#boxSearch1").click();
        }else if($('#searchBoxStatus').val() == 'box2'){
            $("#boxSearch1").click();
            $("#boxSearch2").click();
        }
        if($('#searchBoxStatus').val() == 'box1' || $('#searchBoxStatus').val() == 'box2' || $('#searchBoxStatus').val() == 'box0'){
            COMMON.scrollToAnchor('bodyHome');
        }
    },
    clearAll: function clearAll() {
        $('#form-search').find("input[type='checkbox']").prop('checked', false);
        $('#form-search').find("input[type='text'], select").val('');
        // $('#detailSearch2').find("input[type='checkbox']").prop('checked', false);
    },
    addFavouritePolicy: function () {
        var listPolicy = COMMON.getItemsFromLocalStorage(HOME.NAME_STORAGE);
        if (!HOME.checkSelectedRow()) {
            COMMON.warningPopup(HOME.messageList.MSG_HO_002_EmptyPolicyIds);
        } else {
            $.ajax({
                    type: 'post',
                    url: HOME.URL_FAVOURITE_POLICY,
                    data: {'policyIds': listPolicy},
                    success: function (data) {
                        if (data.success) {
                            COMMON.removeAllItemsFromLocalStorage(HOME.NAME_STORAGE);
                            COMMON.popupMessage(data.msg, function () {
                                    window.location = window.location.href;
                                },
                                'お気に入り施策の登録',
                                undefined,
                                undefined,
                                function () {
                                    window.location = window.location.href;
                                }
                            );
                        } else {
                            COMMON.errorPopup(data.msg);
                        }
                    },
                    error: function (err) {

                    }
                }
            );
        }
    },
    clearSessionStorageWhenClickFromOtherPage: function (filter) {
        if (typeof document.referrer != 'undefined') {
            var referrerLocation = document.createElement('a');
            referrerLocation.href = document.referrer;
            // page reload
            if(performance.navigation.type == 1){
                return false;
            }
            if (referrerLocation.pathname.replace(/\/$/, '') == window.location.pathname.replace(/\/$/, '')
                &&
                ((filter == 'new' && referrerLocation.search.indexOf('filter') == -1 )
                || (referrerLocation.search.indexOf('filter=' + filter) >= 0))) {
                return false;
            }
            COMMON.removeAllItemsFromLocalStorage(HOME.NAME_STORAGE);
        }
    },
    removeFavouritePolicy: function () {
        var listPolicy = COMMON.getItemsFromLocalStorage(HOME.NAME_STORAGE);
        if (!HOME.checkSelectedRow()) {
            COMMON.warningPopup(HOME.messageList.MSG_HO_002_EmptyPolicyIds);
        }else{
            $.ajax({
                    type: 'post',
                    url: HOME.URL_REMOVE_FAVOURITE_POLICY,
                    data: {'policyIds': listPolicy},
                    success: function (data) {
                        if (data.success) {
                            COMMON.removeAllItemsFromLocalStorage(HOME.NAME_STORAGE);
                            COMMON.popupMessage(data.msg, function () {
                                    window.location = window.location.href;
                                },
                                'お気に入りから削除',
                                undefined,
                                undefined,
                                function () {
                                    window.location = window.location.href;
                                });
                        } else {
                            COMMON.errorPopup(data.msg);
                        }
                    },
                    error: function (err) {

                    }
                }
            );
        }
    },
    checkSelectedRow: function () {
        var listIds = COMMON.getItemsFromLocalStorage(HOME.NAME_STORAGE);
        if (listIds == undefined || listIds.length == 0) {
            return false;
        }
        return true;
    },
    toggleAttendFlag: function (element) {
        if($(element).is(':checked')){
            $(".attend-box").each(function (i, obj) {
                $(obj).hide();
            });
            $(".cb-attend").each(function (i, obj) {
                $(obj).prop('checked', true);
            })
        }else{
            $(".attend-box").each(function (i, obj) {
                $(obj).show();
            });
            $(".cb-attend").each(function (i, obj) {
                $(obj).prop('checked', false);
            })
        }
    },
    addSearchHistory: function () {
        var query = '?name=' + encodeURIComponent($('#txtHistoryName').val());
        if($('#ddlHistoryId').is(":visible")){
            query += '&policyId=' + encodeURIComponent($('#ddlHistoryId').val());
        }
        $.ajax({
                type: 'post',
                url: HOME.URL_ADD_SEARCH_HISTORY + query,
                data: $('#form-search').serialize(),
                success: function (data) {
                    if (data.success) {
                        $("#historySave").modal('hide');
                        $("#history-form")[0].reset();
                        COMMON.popupMessage(data.msg, function () {
                                window.location = window.location.href;
                            },
                            '検索条件の保存',
                            undefined,
                            undefined,
                            function () {
                                window.location = window.location.href;
                            });
                    } else {
                        var errorContent = "<ul>";
                        data.errors.forEach(function (error) {
                            errorContent += "<li>" + error + "</li>"
                        });

                        errorContent += "</ul>";
                        $("#errors-list-history").html(errorContent);
                    }
                },
                error: function (err) {

                }
            }
        );
    },
    loadHistory: function () {
        $.ajax({
                type: 'post',
                url: HOME.URL_LOAD_SEARCH_HISTORY,
                data: $('#form-select-history').serialize(),
                success: function (data) {
                    if (data.success) {
                        window.location = window.location.href;
                    } else {
                        var errorContent = "<ul>";
                        data.errors.forEach(function (error) {
                            errorContent += "<li>" + error + "</li>"
                        });

                        errorContent += "</ul>";
                        $("#errors-call-history").html(errorContent);
                    }
                },
                error: function (err) {

                }
            }
        );
    },
    exportCompare: function () {
        var listPolicy = COMMON.getItemsFromLocalStorage(HOME.NAME_STORAGE);
        if (!HOME.checkSelectedRow()) {
            COMMON.warningPopup(HOME.messageList.MSG_HO_002_EmptyPolicyIds);
            return false;
        }
        if (listPolicy.length > 10 || listPolicy.length < 2){
            COMMON.warningPopup(HOME.messageList.MSG_HO_010_MaxMinCompare);
            return false;
        }

        $('#exportIdsCompare').val(listPolicy);
        $('#exportCompareForm').submit();
        $('#exportIdsCompare').val('');

    },
    showPopupLoadHistory: function () {
        if(searchHistory == undefined || searchHistory.length == 0){
            COMMON.warningPopup(HOME.messageList.MSG_HO_012_SearchHistoryEmpty);
            return false;
        }
        $("#searchConditionCall").modal('show');
    },

    showPopupAddHistory: function () {
        var formData = $("#form-search").serializeArray();
        var isValid = false;
        formData.forEach(function (field) {
            if(field['name'] != 'searchBoxStatus' && $.trim(field['value']) != ''){
                isValid = true;
                return false;
            }
        });
        if(!isValid){
            COMMON.warningPopup(HOME.messageList.MSG_HO_013_SearchConditionEmpty);
            return false;
        }
        $("#historySave").modal('show');
    },
    clearError: function (form, validator) {
        $(form).find('input.error').removeClass('error');
        $(form).find('.error-box').html('');
        validator.resetForm();
    }
}