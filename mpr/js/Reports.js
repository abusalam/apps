$(function () {
  $('.chzn')
    .chosen({
      width: "150px",
      no_results_text: "Oops, nothing found!"
    });

  $("#Scheme").chosen({
    width: "250px",
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
        'CallAPI': 'Reports_GetSchemeFunds',
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

  $("#UserID").chosen({
    width: "250px",
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
        'CallAPI': 'Reports_GetWorkFunds',
        'Scheme': $("#Scheme").val(),
        'User': $(this).val()
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

  $(".datePick").datepicker({dateFormat: "dd/mm/yy"}).css({"width": "80px"});
});
