 var n = 1;
$(document).ready(function() {
  $("#theme").click(function(e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url:'http://127.0.0.1/SIGEF/Formations/edit',
      data: {
        id: $("#theme").val(),
        access_token: $("#access_token").val()
      },
      minLength: 1,
      success: function(result) {
        //alert('ok');
        $('#theme-table').append('<tr><td><input name="data[Formation]['+n+'][theme]" type="textarea"  class="mce" /></td></tr>');
        n++;
        
      },
      error: function(result) {
        alert('error');
      }
    });
  });

});