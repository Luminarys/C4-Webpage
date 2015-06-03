function deselect(e) {
  $('.pop').slideFadeToggle(function() {
    e.removeClass('selected');
  });    
}

$(function() {
	$("#contact").mouseover(function() {
	$("<div class='description'> Here is the big fat description box</div>").insertAfter(this);
      	$.get("php/annotation_popup.php?link=true&spec=Zmays&gene=GRMZM2G001272",function(data){
          $('.description').empty().append(data).show();
      });
	$("#contact").click(function() {
		document.location.href ="annotation_query.php?link=true&spec=Zmays&gene=GRMZM2G001272";
	});
}).mouseout(function() {
    $(".description").hide();
    $(".description").remove();
});

  $('#contact').on('click', function() {
      });
});

