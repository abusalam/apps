$(function () {

  var dialog = $("#createNewScheme").dialog({
    autoOpen: false,
    height: 300,
    width: 350,
    modal: true,
    buttons: {
      "Create Scheme": function () {
        $("#frmCreateScheme").submit();
        dialog.dialog("close");
      },
      Cancel: function () {
        dialog.dialog("close");
      }
    }
  });

  $(".chzn").chosen({
    width: "200px",
    no_results_text: "Oops, nothing found!"
  }).change(function () {
    if ($(this).val() === "NewScheme") {
      dialog.dialog("open");
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
          'CallAPI': 'Schemes_GetSchemeTable',
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
    }
  });
  $(".datePicker").datepicker({dateFormat: "yy-mm-dd"}).css({"width": "80px"});
});