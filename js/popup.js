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
		event: 'unfocus'
	}, 
	show: {
		solo: true,
		event: 'click'
	}
});
console.log("Added in popups");
}

function addGraphPopups(){
$(".node").qtip({
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
		event: 'unfocus'
	}, 
	show: {
		event: 'click',
		solo: true
	}
});
console.log("Added in popups");
}
