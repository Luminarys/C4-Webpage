function drawBars(){
	$.get("php/get_expr_map.php?spec=" + $("#spec").val(), function(data) {
		var info = JSON.parse(data);
		$('.slider').remove();
		$('.sample').remove();
		for(var i = info.length-1; i > 0; i--){
			$("#eq").prepend("<span class='slider' id=" + info[i] +">50</span>");
		}
		var s = $(".slider").length;
		$("#eq").css("width",s * 66.5);
		$( ".slider" ).each(function() {
			$("#samples").append("<td class='sample'><span>" + $(this).attr("id")+"</span></td>");
			$("#values").append("<td class='value'><span id='sample" + $(this).attr("id") + "'>50%</span></td>");
			var value = parseInt( $( this ).text(), 10 );
			$( this ).empty().slider({
				value: value,
				range: "min",
				animate: true,
				orientation: "vertical",
				slide: function( event, ui ) {
        				$( "#sample" + $(this).attr("id") ).text( ui.value + "%" );
      				} 
			});
		});
		
	});
}

function getVals(){
	var vals = [];
	$( ".slider" ).each(function() {
		vals.push($(this).slider("option", "value"));	
	});
	console.log(vals);
	return vals;
}

$(document).ready(function() {
	drawBars();
	$('#spec').change(function(){
		drawBars();
	});
	$('#expressionProfileForm').submit(function(e) {
		e.preventDefault();
		var vals = getVals();
		var req = "php/expression_profile_query.php?spec=" + $("#spec").val();
		for(var i = 0; i < vals.length; i++){
			req+="&s" + i + "=" + vals[i];
		}
		console.log(req);
	});
});
