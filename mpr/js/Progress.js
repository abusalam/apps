$(function () {
    $('.chzn')
        .chosen({width: "150px",
            no_results_text: "Oops, nothing found!"
        });

    $("#Work").chosen({width: "300px",
        no_results_text: "Oops, nothing found!"
    }).change(function () {
        $.ajax({
            type: 'POST',
            url: 'AjaxData.php',
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            },
            data: {
                'AjaxToken': $('#AjaxToken').val(),
                'CallAPI': 'Progress_GetWorkStatusJSON',
                'Work': $(this).val()
            }
        }).done(function (data) {
            try {
                $("#PhyPrgLbl").html(data.Progress);
                $("#PhyPrgSlider").data("minVal",data.Progress)
                    .slider("option", "value",data.Progress);
                $("#PhyPrgValue").val(data.Progress);
                $("#txtWorkOrderDate").val(data.WorkOrderDate);
                $("#txtTenderDate").val(data.TenderDate);
                $("#txtBalance").val(data.Balance)
                $.ajax({
                    type: 'POST',
                    url: 'AjaxData.php',
                    dataType: 'html',
                    xhrFields: {
                        withCredentials: true
                    },
                    data: {
                        'AjaxToken': $('#AjaxToken').val(),
                        'CallAPI': 'Progress_GetProgressDetails',
                        'Work': $("#Work").val()
                    }
                }).done(function (data) {
                    try {
                        $('#ProgressTable').html(data);
                    }
                    catch (e) {
                        $('#Msg').html('Server Error:' + e);
                        $('#Error').html(data);
                    }
                }).fail(function (msg) {
                    $('#Msg').html(msg);
                });
            }
            catch (e) {
                $('#Msg').html('Server Error:' + e);
                $('#Error').html(data);
            }
        }).fail(function (msg) {
            $('#Msg').html(msg);
        });
    });

    $("#cmbScheme").chosen({width: "175px",
        no_results_text: "Oops, nothing found!"
    }).change(function () {
        $.ajax({
            type: 'POST',
            url: 'AjaxData.php',
            dataType: 'html',
            xhrFields: {
                withCredentials: true
            },
            data: {
                'AjaxToken': $('#AjaxToken').val(),
                'CallAPI': 'Progress_GetWorksList',
                'Scheme': $("#cmbScheme").val()
            }
        }).done(function (data) {
            try {
                $("#Work").html(data).trigger("chosen:updated");
                //alert(data);
                $.ajax({
                    type: 'POST',
                    url: 'AjaxData.php',
                    dataType: 'html',
                    xhrFields: {
                        withCredentials: true
                    },
                    data: {
                        'AjaxToken': $('#AjaxToken').val(),
                        'CallAPI': 'Progress_GetWorkDetails',
                        'Scheme': $("#cmbScheme").val()
                    }
                }).done(function (data) {
                    try {
                        $('#DataTable').html(data);
                    }
                    catch (e) {
                        $('#Msg').html('Server Error:' + e);
                        $('#Error').html(data);
                    }
                }).fail(function (msg) {
                    $('#Msg').html(msg);
                });
            }
            catch (e) {
                $('#Msg').html('Server Error:' + e);
                $('#Error').html(data);
            }
        }).fail(function (msg) {
            $('#Msg').html(msg);
        });

    });

    $(".datePick").datepicker({dateFormat:"dd/mm/yy"}).css({"width": "80px"});

    $("#PhyPrgSlider").slider({
        range: "min",
        value: $(this).data("minVal"),
        min: 0,
        max: 100,
        slide: function (event, ui) {
            $("#PhyPrgValue").val(ui.value);
            $("#PhyPrgLbl").html(ui.value);
        },
        stop: function( event, ui ) {
            var minVal=$(this).data("minVal");
            if(ui.value<minVal){
                $(this).slider( "option", "value",minVal);
                $("#PhyPrgValue").val(minVal);
                $("#PhyPrgLbl").html(minVal);
                return false;
            }
        }
    }).find(".ui-slider-range").css({
        "background": "#00ff00"
    }).find(".ui-slider-handle").css({
        "border-color": "#00ff00"
    });
});
