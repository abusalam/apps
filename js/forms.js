/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
  $('input[type="submit"]').button();
  $('#UserPass').on({"blur": function() {
      $(this).val($.md5($.md5($(this).val()).toString()
              + $.md5($('#LoginToken').val())));
    }
  });

  $('.jQuery-ButtonSet').buttonset();

});
