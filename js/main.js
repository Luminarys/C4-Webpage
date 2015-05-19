$(document).ready(function() {

	var prev_gene = "";
	var prev = "";
	$('#entryForm').children().hide();

	//For some reason the specific fields don't work, but this is fine
	$('*').keyup( function() {
		console.log("key released");
        	table.draw();
    	} );
	
	$("#singleGeneQuery").click(function() {
		prev="singleGene";
		$("#querySelection").hide();
		$("#singleGeneForm").show();
	});

	$("#multiGeneQuery").click(function() {
		prev="multiGene";
		$("#querySelection").hide();
		$("#multiGeneForm").show();
	});

	$("#modMemberQuery").click(function() {
		prev="modMember";
		$("#querySelection").hide();
		$("#modMemberForm").show();
	});

	$("#expressionQuery").click(function() {
		prev="expression";
		$("#querySelection").hide();
		$("#expressionForm").show();
	});

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
