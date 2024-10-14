$(document).ready(function () {
	$('.print').click(function(event){
		event.preventDefault();
		window.open($(this).attr('href'), 'ReceiptWin', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=290,height=500')
	});
});