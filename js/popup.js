$(
function() {
		$(".popup").click(function() {
			document.location.href = "/annotation_query.php" + $(this).attr("value");
		});
});
$(document).ready( function() {
	addPopups();
});

function addPopups(){
$(".popup").qtip({
	content: {
		text: function(event, api){
			$.ajax({
				url: "php/annotation_popup.php" + $(this).attr("value")
			})
			.then(function(content){
				api.set('content.text', content);
			}, function(xhr, status, error) {
				api.set('content.text', status + ': ' + error);	
			});
			return 'Loading...';
		}
	},	
	hide: {
		fixed: true,
		//event: false,
		//inactive: 3000,
		delay: 500
	},
	show: {
		solo: true
	}
});

}
