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
		e.preventDefalt();
		var $inputs = $('#geneQuery :input');
		//Intialize parameters for filtering
		var values = { };
		$inputs.each(function() {
			console.log($(this).val());
			values['gene'] = $(this).val();
		});
		console.log(values['gene']);
		$.get('/query_accept.php?gene='+values['gene'], function(data) {
		console.log(data);
		$('#qTable').append(data);
		});
	});

} );

