function debounce(fn, delay) {
  var timer = null;
  return function () {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      fn.apply(context, args);
    }, delay);
  };
}

$(document).ready(function() {
      	$("#header").load("../header.html"); 
      	$("#footer").load("../footer.html"); 

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
