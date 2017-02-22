/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
  $('input[type="submit"]').button();
  $('input[type="reset"]').button();


  $('#StartDate').datepicker({
    dateFormat: 'dd-mm-yy',
    showOn: "both",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true
  });
  $('#AlotmentDate').datepicker({
    dateFormat: 'dd-mm-yy',
    showOn: "both",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true
  });
  $('#TenderDate').datepicker({
    dateFormat: 'dd-mm-yy',
    showOn: "both",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true
  });
  $('#WorkOrderDate').datepicker({
    dateFormat: 'dd-mm-yy',
    showOn: "both",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true
  });
  $('#ReportDate').datepicker({
    dateFormat: 'dd-mm-yy',
    showOn: "both",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true
  });
  $("#DeptID")
          .chosen({width: "200px",
            no_results_text: "Oops, nothing found!"})
          .change(function() {
            var Options = '<option value=""></option>';
          });
  $("#SectorID").chosen({width: "200px",
    no_results_text: "Oops, nothing found!"})
          .change(function() {
            var Options = '<option value=""></option>';
          });
  $("#SchemeID").chosen({width: "200px",
    no_results_text: "Oops, nothing found!"})
          .change(function() {
            var Options = '<option value=""></option>';
          });
  $("#ProjectID").chosen({width: "200px",
    no_results_text: "Oops, nothing found!"})
          .change(function() {
            var Options = '<option value=""></option>';
          });

  $.ajax({
    type: 'POST',
    url: 'AjaxData.php',
    dataType: 'html',
    xhrFields: {
      withCredentials: true
    },
    data: {
      'AjaxToken': $('#AjaxToken').val(),
      'CallAPI': 'GetChosenData'
    }
  })
          .done(function(data) {
            try {
              var DataResp = $.parseJSON(data);
              delete data;
              $('#AjaxToken').val(DataResp.AjaxToken);
              $('#Msg').html(DataResp.Msg);
              $('#ED').html(DataResp.RT);
              var Options = '<option value=""></option>';
              $.each(DataResp.DeptID,
                      function(index, value) {
                        //option for Departments...
                        Options += '<option value="' + value.DeptID + '">'
                                + value.DeptID + ' - ' + value.DeptName
                                + '</option>';
                      });
              $('#DeptID').html(Options)
                      .trigger("chosen:updated");
              $('#DeptID').data('DeptID', DataResp.DeptID);
              //option for Sectors..
              Options = '<option value=""></option>';
              $.each(DataResp.SectorID,
                      function(index, value) {
                        Options += '<option value="' + value.SectorID + '">'
                                + value.SectorID + ' - ' + value.SectorName
                                + '</option>';
                      });
              $('#SectorID').html(Options)
                      .trigger("chosen:updated");
              $('#SectorID').data('SectorID', DataResp.SectorID);
              //option for Schemes...
              Options = '<option value=""></option>';
              $.each(DataResp.SchemeID,
                      function(index, value) {
                        Options += '<option value="' + value.SchemeID + '">'
                                + value.SchemeID + ' - ' + value.SchemeName
                                + '</option>';
                      });
              $('#SchemeID').html(Options)
                      .trigger("chosen:updated");
              $('#SchemeID').data('SchemeID', DataResp.SchemeID);
              //option for projects...
              Options = '<option value=""></option>';
              $.each(DataResp.ProjectID,
                      function(index, value) {
                        Options += '<option value="' + value.ProjectID + '">'
                                + value.ProjectID + ' - ' + value.ProjectName
                                + '</option>';
                      });
              $('#ProjectID').html(Options)
                      .trigger("chosen:updated");
              $('#ProjectID').data('ProjectID', DataResp.ProjectID);
              delete DataResp;
              $("#Msg").html('');
            }
            catch (e) {
              $('#Msg').html('Server Error:' + e);
              $('#Error').html(data);
            }
          })
          .fail(function(msg) {
            $('#Msg').html(msg);
          });

  $("#PhysicalSlider").slider({
    range: "min",
    value: 0,
    min: 0,
    max: 100,
    slide: function(event, ui) {
      $("#lblPhysicalProgress").html("Physical Progress: " + ui.value + "%");
      $("#PhysicalProgress").val(ui.value);
    }
  });
  $("#FinancialSlider").slider({
    range: "min",
    value: 0,
    min: 0,
    max: 100,
    slide: function(event, ui) {
      $("#lblFinancialProgress").html("Financial Progress: " + ui.value + "%");
      $("#FinancialProgress").val(ui.value);
    }
  });

});
