/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
  $('input[type="submit"]').button();
  $('#UserPass').on({
    "blur": function () {
      $(this).val(sha512(sha512($(this).val()).toString()
        + sha512($('#LoginToken').val())));
    }
  });

  $('.jQuery-ButtonSet').buttonset();

});
