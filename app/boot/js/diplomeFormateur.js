 var n = 1;
$(document).ready(function() {
  $("#diplome").click(function(e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url:'http://127.0.0.1/SIGEF/Diplome/formateurdiplome',
      data: {
        id: $("#diplome").val(),
        access_token: $("#access_token").val()
      },
      minLength: 1,
      success: function(result) {
        //alert('ok');
        $('#diplome-table').append('<tr><td><input class="diplome" name="data[Diplome]['+n+'][annee]" type="text" /></td><td><input class="diplome" name="data[Diplome]['+n+'][intitule]" type="text" /></td><td><input class="diplome" name="data[Diplome]['+n+'][niveau]" type="text" /></td><td><input class="diplome" name="data[Diplome]['+n+'][nomfichier]" type="file" /></td><td><a href="javascript:;" class="remove">Supprimer</a></td></tr>');
        n++;
        
      },
      error: function(result) {
        alert('error');
      }
    });
  });

});