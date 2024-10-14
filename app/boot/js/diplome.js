 
$(document).ready(function() {
  $("#diplome").click(function(e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url:'http://127.0.0.1/SIGEFCEMEAU/Diplome/edit',
      data: {
        id: $("#diplome").val(),
        access_token: $("#access_token").val()
      },
      minLength: 1,
      success: function(result) {
        //alert('ok');
        $('#diplome-table').append('<tr><td><input class="diplome" name="data[Diplome][][cv]" type="file" /></td><td><a href="javascript:;" class="remove">Supprimer</a></td></tr>');
        n++;
        
      },
      error: function(result) {
        alert('error');
      }
    });
  });

});

$(document).on('click', '.remove', function(e){
    e.preventDefault();
    $(this).closest('tr').remove();
	if($('.diplome').length == 0) $('.enregistrer').css('display','none');
    return;
})