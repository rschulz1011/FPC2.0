var buildPickTable = function(parameters) {
	
	var $pickTableDiv = $("div#pickForm");
	
	getPickInfo(parameters)
	.done(function(pickInfo){
		console.log(pickInfo)
		
		var $pickTable = $("<table><tr><th></th><th>Game</th>" +
				"<th>Favorite</th><th>PICK</th><th>Confidence Pts</th>" +
				"<th>Lock Time</th><th>Points</th></tr></table>");
		$pickTableDiv.append($pickTable);
		
		pickInfo.forEach(function(pick,index){
			var $newTr = $("<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>");
			
			if (index % 2 == 1) {
				$newTr.addClass("shaded");
			}

			$pickTable.append($newTr);
			populatePickRow(pick,$newTr);
		});
		
		
	});
	
}

var populatePickRow = function(pick,$newTr){
	console.log("pickrow");
	
	var $tds = $newTr.find("td");
	$($tds[0]).append(pick.pickname);
	if (pick.aloc !== null) {
		$($tds[1]).append(pick.aloc+" @ "+pick.hloc);
		
		var spreadString
		if (pick.spread>0) {
			spreadString = pick.aloc+" by "+pick.spread;
		}
		else if (pick.spread<0) {
			spreadString = pick.hloc+" by "+pick.spread*-1;
		}
		else {
			spreadString = "Even";
		}
		
		$($tds[2]).append(spreadString);
	}
	
	
}

var getPickInfo = function(parameters) {
	
	var promise = new $.Deferred();
	
	$.ajax({
		url: "pickHandler.php",
		data: {
			weeknum: parameters.weeknum,
			compId: parameters.compId,
			username: parameters.username,
		},
		dataType: "json",
		
	})
	.done(function(pickData){
		promise.resolve(pickData);
	})
	.fail(function(jqXHR,textStatus,error){
		console.log(textStatus);
		console.log(error);
	});
	
	return promise;
}
