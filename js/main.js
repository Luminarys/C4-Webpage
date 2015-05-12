$(document).ready(function() {
    $('#basicQuery').DataTable();
	$('#filterForm').submit(function() {
		var $inputs = $('#filterForm :input');
		console.log($inputs);
		//Intialize parameters for filtering
		var column = "";	
		var lessThan = 0;	
		var value = 0;	
		var values = { };
		$inputs.each(function() {
			console.log($(this).val());
			console.log(this.name);
			values[this.name] = $(this).val();
		});
	});

	$('#geneQuery').submit(function(e) {
		//Prevents the webpage from directing to the GET url
		e.preventDefault();
		var gene = $('#gene').val();
		$.get('query_accept.php?gene=' + gene, function(data) {
			$('#qTable').append(data);
		});
	});

} );

