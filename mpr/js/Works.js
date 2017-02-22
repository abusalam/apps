$(function () {
    $(".chzn").chosen({width: "200px",
        no_results_text: "Oops, nothing found!"
    });
    $(".datePicker").datepicker({dateFormat:"dd/mm/yy"}).css({"width": "80px"});

    $("#Scheme").chosen({width: "175px",
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
                'CallAPI': 'Works_GetWorks',
                'Scheme': $(this).val()
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
    });

    $("#MprMapID").chosen({width: "275px",
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
                'CallAPI': 'Works_GetWorksForSanction',
                'Scheme': $("#Scheme").val(),
                'MprMapID': $(this).val()
            }
        }).done(function (data) {
            try {
                $("#WorkID").html(data).trigger("chosen:updated");
                $.ajax({
                    type: 'POST',
                    url: 'AjaxData.php',
                    dataType: 'html',
                    xhrFields: {
                        withCredentials: true
                    },
                    data: {
                        'AjaxToken': $('#AjaxToken').val(),
                        'CallAPI': 'Works_GetWorks',
                        'Scheme': $("#Scheme").val(),
                        'MprMapID': $("#MprMapID").val()
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

    $("#WorkID").chosen({width: "460px",
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
                'CallAPI': 'Works_GetSanctions',
                'WorkID': $(this).val()
            }
        }).done(function (data) {
            try {
                $('#SanctionData').html(data);
            }
            catch (e) {
                $('#Msg').html('Server Error:' + e);
                $('#Error').html(data);
            }
        }).fail(function (msg) {
            $('#Msg').html(msg);
        });
    });
});