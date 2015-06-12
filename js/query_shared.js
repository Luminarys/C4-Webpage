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
	$("#goBack").hide();

	$(".backToQuery").click(function() {
		window.location.href='index.php';
	});

	$("#backToInput").click(function() {
		$("#goBack").hide();
		$("#MultiGeneQueryExpression").hide();
		$("#info").hide();
		$("#qTable").empty();
		$(".entryForm").show();
		$("#inGraphOpts").hide();
	});
	console.log("Shared functions are ready");
});
