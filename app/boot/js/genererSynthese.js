<script>
    function genPDF(){
		html2canvas(document.getElementById("testDIV"),
		{
			onrendered: function(canvas){
				var doc = new jsPDF();
				doc.save('synthese.pdf');
			}
		});
	}
</script>