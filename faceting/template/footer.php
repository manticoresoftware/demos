<script>
	$(document).ready(function() {
	$(':radio,:checkbox').change(function() {
		
			$("#faceted").trigger('submit');
	
	});
		$(':reset').click(function(){
		
		location.search ='';
	});
	$('.reset_facet').click(function(){
		$('input[name^='+$(this).attr('data-target')+']').removeAttr('checked');
		$("#faceted").trigger('submit');
		return false;
			
	});		
});
</script>	
