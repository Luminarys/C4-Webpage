$(document).ready(function() {

	var prev_gene = "";
	var prev = "";
	$('#entryForm').children().hide();
	
	$("#backToInput").click(function() {
		$("#goBack").hide();
		$("#qTable").empty();
		$("#" + prev + "Form").show();
		$('#lower-rect').removeAttr('style').css("margin-top", "450px");
	});

	$(".backToQuery").click(function() {
		window.location.href='index.html';
	});
});
