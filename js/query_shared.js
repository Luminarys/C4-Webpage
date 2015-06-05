$(document).ready(function() {

	$("#goBack").hide();

	$(".backToQuery").click(function() {
		window.location.href='index.html';
	});

	$("#backToInput").click(function() {
		$("#goBack").hide();
		$("#MultiGeneQueryExpression").hide();
		$("#info").hide();
		$("#qTable").empty();
		$(".entryForm").show();
		$('#lower-rect').removeAttr('style').css("margin-top", "450px");
		$("#inGraphOpts").hide();
	});
	console.log("Shared functions are ready");
});
