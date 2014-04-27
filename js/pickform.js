var serverTime;
var teams;
var collegeSurvivorTeams;
var proSurvivorTeams;

var buildPickTable = function(parameters) {
	
	serverTime = new Date(parameters.serverTime);
	teams = parameters.teams;
	proSurvivorTeams = parameters.proSurvivorTeams;
	collegeSurvivorTeams = parameters.collegeSurvivorTeams;
	
	var $pickTableDiv = $("div#pickForm");
	
	getPickInfo(parameters)
	.done(function(pickInfo){
		console.log(pickInfo)
		
		var $pickTable = $("<table><tr><th></th><th>Game</th>" +
				"<th>Favorite</th><th>PICK</th><th>Conf</th>" +
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
		
		$pickTable.find("td").attr("align","center");
		
	});
	
}

var populatePickRow = function(pick,$newTr){
	console.log("pickrow");
	
	var $tds = $newTr.find("td");
	
	if (pick.picktype !== "OTHER") {
		$($tds[0]).append(pick.pickname);
	}
	else {
		$($tds[1]).append(pick.pickname);
	}
	
	
	if (pick.aloc !== null) {
		
		if (pick.picktype !== "OTHER") {
			$($tds[1]).append(pick.aloc+" @ "+pick.hloc);
		}
		
		var spreadString
		if (pick.spread>0) {
			spreadString = pick.aloc+" by "+pick.spread;
		}
		else if (pick.spread<0) {
			spreadString = pick.hloc+" by "+pick.spread*-1;
		}
		else if (pick.spread==="0"){
			spreadString = "Even";
		}
		$($tds[2]).append(spreadString);
	}
	
	fillPickCell(pick, $($tds[3]))

	if (pick.confpts !== null && pick.confpts !== "0"){
		$($tds[4]).append(pick.confpts);
	}
	
	var date = new Date(pick.locktime);
	var dayOfWeek = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
	var month = ["Jan","Feb","Mar","Apr","May","June","July","Aug","Sep","Oct","Nov","Dec"];
	
	var day = dayOfWeek[date.getDay()];
	var month = month[date.getMonth()];
	var hours = date.getHours();
	var ampm = "AM";
	if (hours>12) {hours=hours-12; ampm="PM";} else if (hours===0) {hours = 12;}
	var minutes = "0";
	minutes = minutes + date.getMinutes();
	minutes = minutes.substr(-2);
	
	if (date.toJSON()!==null) {
		$($tds[5]).append(day+", "+month+" "+date.getDate()+" "+hours+":"+minutes+" "+ampm);
	}
	$($tds[6]).append(pick.pickpts);	
}

var fillPickCell = function(pick,$td) {
	
	var locked = serverTime>new Date(pick.locktime);
	
	if (locked) {
		switch(pick.picktype)
		{
		case "ATS":
		case "ATS-C":
			$td.append(teams[pick.pick]);
			if (pick.correctans===null){
				$td.addClass("pendingpick");
			}
			else if (pick.correctans===pick.pick) {
				$td.addClass("goodpick");
			}
			else if (pick.correctans==="-1") {
				$td.addClass("pushpick");
			}
			else {
				$td.addClass("badpick");
			}
			break;
		case "S-COL":
		case "S-PRO":
			$td.append(teams[pick.pick]);
			if (pick.pickpts>0) {
				$td.addClass("goodpick");
			}
			else if (pick.pickpts===null) {
				$td.addClass("pendingpick");
			}
			else {
				$td.addClass("badpick");
			}
			break;
		case "OTHER":
			if (pick.pick=="1"){
				$td.append(pick.option1);
			}
			else if (pick.pick=="2"){
				$td.append(pick.option2);
			}
			
			if (pick.correctans==null){
				$td.addClass("pendingpick");
			}
			else if (pick.correctans==pick.pick){
				$td.addClass("goodpick");
			}
			else {
				$td.addClass("badpick");
			}
			
			break;
		}
	}
	else {
		switch (pick.picktype)
		{
		case "ATS":
		case "ATS-C":
			createPickList($td,[parseInt(pick.option1),parseInt(pick.option2)],pick.pick,pick.pickID);
			break;
		case "S-PRO":
			createPickList($td,proSurvivorTeams,pick.pick,pick.pickID);
			break;
		case "S-COL":
			createPickList($td,collegeSurvivorTeams,pick.pick,pick.pickID);
			break;
		}	
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

var createPickList = function($td,teamList,selection,pickId)
{
	var $newPickList = $("<select>",{
		"class" : "pick",
		"data-pickId": pickId,
	});
	
	var $blankOption = $("<option>",{
		val: 0,
	}).append(" ");
	if (parseInt(selection)===0){
		$blankOption.prop("selected",true);
	}
	$newPickList.append($blankOption);
	
	for (var index=0;index<teamList.length;index++)
	{
		var $option = $("<option>",{
			val: teamList[index],
		}).append(teams[teamList[index]]);
		
		if (parseInt(selection) === teamList[index]) {
			$option.prop("selected",true);
		}
		$newPickList.append($option);
	}
	
	$td.append($newPickList);
	
}
