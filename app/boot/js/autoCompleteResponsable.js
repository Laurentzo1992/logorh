 var n = 999;
$(document).on("focus",".autocomp-filter",function(e) {
    if ( !$(this).data("autocomplete") ) {
        $(this).autocomplete({ 
            source: function (request, response) {
                $.ajax({
                    type: "POST",
                    url:'http://127.0.0.1/SIGEF/Sessionresponsables/getresponsable',
                    data: {s:request.term},
                    success: function(data){
                        response($.map(data, function (key, value) {
                            return {
                                label: key,
                                id: value
                            };
                        }));
                    },
                    dataType: 'json'
                });
            },
            minLength: 1,
            select: function( event, ui ) {
                event.preventDefault();
                var that = $(this);
                that.val('');
				$('#responsable-table').append('<tr class="tr1" style="width:250px;"><td>'+ui.item.label+'<input class="responsable" name="data[Sessionresponsable]['+n+'][responsable_id]" type="hidden" value="'+ui.item.id+'"/>	</td><td><a href="javascript:;" class="remove">Supprimer</a></td></tr>');
				if($('.responsable').length > 0) $('.enregistrer').css('display','table-row');
				console.log($('.responsable').length);
				that.focus();
				n++;
            },
            change: function( event, ui ) {
                if (ui.item === null) {
                    var that = $(this);
                    that.val('');
                }
            }
        });
    }
});
 
 $(document).on('click', '.remove', function(e){
    e.preventDefault();
    $(this).closest('tr').remove();
	if($('.responsable').length == 0) $('.enregistrer').css('display','none');
    return;
})