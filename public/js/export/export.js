$(function(){
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth();
    var defaultDate = new Date(year, month, 1);
    
    $('#exportTimeModal').on('shown.bs.modal', function (e) {
        $("#startDateBox").datepicker("setDate", defaultDate); 
        $("#endDateBox").datepicker("setDate", defaultDate); 
        
        $("#startMonth-error").hide().html("");
        $("#startMonth").removeClass("error");   
        
        $("#endMonth-error").hide().html("");
        $("#endMonth").removeClass("error");
    });
    
    $("#frmExportData").submit(function(){
        $("#startMonth-error").hide().html("");
        $("#startMonth").removeClass("error");
        
        $("#endMonth-error").hide().html("");
        $("#endMonth").removeClass("error");
        
        var startMonth = $("#startMonth").val();
        var endMonth = $("#endMonth").val();
    
        var startMonthDate = new Date(startMonth + "/01 00:00:00");
        var endMonthDate = new Date(endMonth + "/01 00:00:00");
        
        if(startMonth!="" && endMonth!="" && startMonthDate>endMonthDate){
            $("#startMonth-error").show().html("募集開始日が募集終了日以下であること。");
            $("#startMonth").addClass("error");
            return false;
        }
        
        if(startMonth!="" && startMonthDate>defaultDate) {
            $("#startMonth-error").show().html("募集開始日が現在の日付以下であること。");
            $("#startMonth").addClass("error");
            return false;   
        }
        
        if(endMonth!="" && endMonthDate>defaultDate) {
            $("#endMonth-error").show().html("募集終了日が現在の日付以下であること。");
            $("#endMonth").addClass("error");
            return false;   
        }
    });
    
    $('#startDateBox').datepicker({
        autoclose: true,
        minViewMode: 1,
        clearBtn: true,
        language: "ja",
        format: 'yyyy/mm'
    });
    
    $('#endDateBox').datepicker({
        autoclose: true,
        minViewMode: 1,
        clearBtn: true,
        language: "ja",
        format: 'yyyy/mm'
    });
    
    $(".btnExportTotalLoginByRole").click(function(){
        $("#exportTimeModal .modal-title").text("ログイン数集計");
        $("#exportTimeModal #exportType").val(1);
        $("#exportTimeModal").modal({backdrop:'static'});
    });
    
    $(".btnExportLoginData").click(function(){
        $("#exportTimeModal .modal-title").text("ログインデータ出力");
        $("#exportTimeModal #exportType").val(2);
        $("#exportTimeModal").modal({backdrop:'static'});
    });
    
    $(".btnExportPolicyStatistic").click(function(){
        $("#exportTimeModal .modal-title").text("施策情報閲覧等回数集計");
        $("#exportTimeModal #exportType").val(3);
        $("#exportTimeModal").modal({backdrop:'static'});        
    });
});