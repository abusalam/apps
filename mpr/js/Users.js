$(function () {

    $(".chzn").chosen({width: "300px",
        no_results_text: "Oops, nothing found!"
    }).change(function () {
        if ($(this).val() === "NewUser") {
            $("#frmUsers").submit();
        } else {
            $.ajax({
                type: 'POST',
                url: 'AjaxData.php',
                dataType: 'html',
                xhrFields: {
                    withCredentials: true
                },
                data: {
                    'AjaxToken': $('#AjaxToken').val(),
                    'CallAPI': 'Users_GetUserData',
                    'UserID': $(this).val()
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
    });
    $(".datePicker").datepicker({dateFormat:"dd/mm/yy"}).css({"width": "80px"});
});