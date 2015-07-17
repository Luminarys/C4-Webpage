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
	var height = $(document).height();
	$("#contents").css("min-height", height - 400);
	$("#goBack").hide();

	$(".backToQuery").click(function() {
		window.location.href='index.php';
	});

	$("#backToInput").click(function() {
		$("#goBack").hide();
		$("#toggleNames").remove();
		$("#MultiGeneQueryExpression").hide();
		$("#info").hide();
		$("#orthoQueryForm").show();
		$("#qTable").empty();
		$(".entryForm").show();
		$("#entryForm").show();
		$("#inGraphOpts").hide();
	});
	console.log("Shared functions are ready");
});
