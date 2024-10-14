<script type="text/javascript">
	function printlayer('synthese'){
		var generator = window.open(",'name,");
		var layetext = document.getElementById(layer);
		generator.document.write(layetext.innerHTML.replace("Print Me"));
		generator.document.close();
		generator.print();
		generator.close();
	}	
</script>