<html>
<head>



<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>


<script>

$(document).ready( function() {

	console.log('test');

	$.ajax({
	
	url: 'get_schedule_html.php',
	dataType: 'html',
	complete: function (data) {
	
		var tempdate, temptime, tempaway, temphome;
		
		console.log('Success!');
		$("div#schedulepage")[0].style.display = 'none';
		$("div#schedulepage")[0].innerHTML = data.responseText;
		$("div.bg-elements")[0].style.backgroundImage = 'none';
	
		$("div#schedulepage").find("table.tablehead").each( function () {
		
			tempdate = $(this).find("tr.stathead > td").text();
			
			$(this).find("tr.oddrow ,tr.evenrow").each( function () {
			
				temptime = $(this).find("td:nth-child(1)")[0].innerHTML;
				
				temphome = $(this).find("td:nth-child(2) > a")[0].innerHTML;
				tempaway = $(this).find("td:nth-child(2) > a")[1].innerHTML;
			
				console.log(tempdate + "  " +  temptime + "  " + temphome + " at " + tempaway);
			
			});
			
		
		});
	
	},
	
	
	
	});

});

</script>

<body>

<h1>Hello</h1>

<div id="schedulepage">
</div>

<div id="testoutput">
</div>


</body>

</html>