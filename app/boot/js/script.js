$(document).ready(function () {
	$('.checkAll').click(function(){
		$('.' + $(this).attr('id') + 'Item').prop('checked', this.checked);
	});
	
	$('.groupAction').click(function(event){
		event.preventDefault();
		var link = $(this);
		var groupArray = $('.' + link.attr('ref') + 'CheckItem:checked').map(function(){ return this.value}).get();
		if(typeof groupArray !== 'undefined' && groupArray.length >0){
			var conf = true;
			if(link.hasClass('confirm')){
				var conf = confirm('Confirmer suppression de donnees');
			}
			if(conf){
				window.location = link.attr('href')+groupArray.join('|');
			}
		}else{
			alert('Veuillez selectionner un element avant d\'appliquer cette action');
		}
	});	
	
	$('.table tr').click(function(event) {
		if (event.target.type !== 'checkbox') {
		  //$(':checkbox', this).trigger('click');
		}
	});
});