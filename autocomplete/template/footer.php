
<script src="/autocomplete/js/bootstrap-typeahead.js"></script>
<script>

$(document).ready(function(){
$('#query').typeahead({
	fitToElement: true,
	autoSelect:true,
	minLength:1,
	source: function(query, process) {
		return $.ajax({	
			type: 'GET',
			url: window.location.pathname+ '/ajax_suggest.php?q=' + query,
			dataType: 'json',
			success: function (data) { 
				process(data.data);
			}
		});
	},
	matcher: function (item) {
        var it = jQuery('<p>'+this.displayText(item)+'</p>').text();
        return ~it.toLowerCase().indexOf(this.query.toLowerCase().trim());
    },
	updater: function (item) {
			 return  jQuery('<p>'+this.displayText(item)+'</p>').text();
    },
	}).on('keyup',this,function(e){
		  if (event.key == "Enter") {
				$('#search_form').submit();
		  }
	});
});

</script>