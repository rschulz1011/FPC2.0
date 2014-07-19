$(document).ready(function() {
	$("a.comp-link span.makepicks").on("click",function(event,span){
		console.log("test");
		event.preventDefault();
		event.stopPropagation();
		window.location.href = event.currentTarget.getAttribute("onclick");
	})
});