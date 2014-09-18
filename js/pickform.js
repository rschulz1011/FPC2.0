var serverTime;
var teams;
var collegeSurvivorTeams;
var proSurvivorTeams;
var $confPtsSelect;

var buildPickTable = function(parameters) {
	
	serverTime = new Date(parameters.serverTime);
	teams = parameters.teams;
	proSurvivorTeams = parameters.proSurvivorTeams;
	collegeSurvivorTeams = parameters.collegeSurvivorTeams;
	
	var $pickTableDiv = $("div#pickForm");
	var $pickStatusDiv = $("div#pickStatus");
	
	getPickInfo(parameters)
	.done(function(pickInfo){

		var $pickTable = $("<table><tr><th></th><th>Game</th>" +
				"<th>Favorite</th><th>PICK</th><th>Conf</th>" +
				"<th>Lock Time</th><th>Points</th></tr></table>");
		$pickTableDiv.append($pickTable);
		
		$confPtsSelect = buildConfPtsSelect(pickInfo);
		
		pickInfo.forEach(function(pick,index){
			var $newTr = $("<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>");
			
			if (index % 2 == 1) {
				$newTr.addClass("shaded");	
			}		
			$newTr.attr("data-pickId",pick.pickID);

			$pickTable.append($newTr);
			populatePickRow(pick,$newTr);
			console.log("pick"+index);
		});
		
		$pickTable.find("td").attr("align","center");
		addPickChangeEvents(parameters,$pickTable,$pickStatusDiv);
		
	});
	
}

var addPickChangeEvents = function(parameters,$pickTable,$pickStatusDiv){
	$pickTable.find("select.pick").on("change",function(){
		pickSubmitParameters = getPickParameters(parameters);
		$pickStatusDiv.empty().append("Processing Picks...");
		$pickStatusDiv.removeClass("success").removeClass("error").addClass("pending");
		$.ajax({
			url: "pickHandler.php",
			method: "post",
			data: {
				username: parameters.username,
				password: parameters.password,
				picks: JSON.stringify(pickSubmitParameters),
				compId: parameters.compId,
				weeknum: parameters.weeknum,
			},
			dataType: "json",
			})
			.done(function(pickInfo){
				if (pickInfo['error']!==undefined)
				{
					$pickStatusDiv.empty().append(pickInfo['error']);
					$pickStatusDiv.removeClass("success").removeClass("pending").addClass("error");
				}
				else {
					console.log('picks updated');
					$pickStatusDiv.empty().append("Picks Saved");
					$pickStatusDiv.removeClass("pending").removeClass("error").addClass("success");
				
					pickInfo.forEach(function(pick,index){
						populatePickRow(pick,$("tr[data-pickId="+pick.pickID+"]"));
					});
					addPickChangeEvents(parameters,$pickTable,$pickStatusDiv);
				}
				
			})
			.fail(function(){
				$pickStatusDiv.empty().append("Error Saving Picks");
				$pickStatusDiv.removeClass("success").removeClass("pending").addClass("error");
			});
	});
}

var getPickParameters = function(parameters){
	
	pickParameters = [];
	$("select.pick").each(function(index,select){
		var $select = $(select);
		if (!$select.hasClass("confPts")){
			var pick = {}
			pick.pickId = parseInt($select.attr("data-pickId"));
			pick.pickType = $select.attr("data-picktype");
			pick.pick = parseInt($select.val());
			pickParameters.push(pick);
		}
		else {
			pickParameters.forEach(function(pick,index){
				if (parseInt(pick.pickId) === parseInt($select.attr("data-pickId"))) {
					pickParameters[index].confPts = parseInt($select.val());
				}
			});
		}
	});
	return pickParameters;
}

var buildConfPtsSelect = function(pickInfo){
	
	var numConfPicks = 0;
	var exclusions = [];
	
	pickInfo.forEach(function(pick,index){
		if (pick.picktype==="ATS-C"){
			numConfPicks++;
			if (isLocked(pick.locktime)){
				exclusions.push(parseInt(pick.confpts));
			}
		}
	});
	
	var $select = $("<select>",{
		class: "pick confPts",
	});
	
	$blankOption = $("<option>").append(" ");
	$select.append($blankOption);
	
	for (var index=0;index<numConfPicks;index++){
		if (exclusions.indexOf(index+1)===-1) {
			var $option = $("<option>").append((index+1).toString());
			$select.append($option);
		}
	}
	return $select;
}

var populatePickRow = function(pick,$newTr){
	console.log("pickrow");
	
	var $tds = $newTr.find("td").empty();
	
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

	if (pick.picktype == "ATS-C"){
		var locked = isLocked(pick.locktime);

		if (locked) {
			$($tds[4]).append(pick.confpts);
		}
		else {
			addConfPtsSelect($($tds[4]),pick.pickID,pick.confpts);
		}
	}
	
	var date = parseDate(pick.locktime);
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
	if ((pick.picktype==="S-PRO" || pick.picktype==="S-COL") && pick.opponent)
	{
		$($tds[5]).append(" ("+teams[pick.opponent]+")");
	}
	
	$($tds[6]).append(pick.pickpts);	
}

var parseDate = function(input) {
	  var parts = input.match(/(\d+)/g);
	  return new Date(parts[0], parts[1]-1, parts[2], parts[3], parts[4],parts[5]);
}

var isLocked = function(locktime){
	var locked = serverTime>new Date(locktime);
	return locked;
}

var addConfPtsSelect = function($td,pickId,confPts){
	$newConfPtsSelect = $confPtsSelect.clone();
	$newConfPtsSelect.attr("data-pickId",pickId);
	$newConfPtsSelect.val(confPts);
	$td.append($newConfPtsSelect);
}

var fillPickCell = function(pick,$td) {
	
	var locked = isLocked(pick.locktime);
	
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
			createPickList($td,[parseInt(pick.option1),parseInt(pick.option2)],pick.pick,pick.pickID,pick.picktype);
			break;
		case "S-PRO":
			createPickList($td,proSurvivorTeams,pick.pick,pick.pickID,pick.picktype);
			break;
		case "S-COL":
			if (pick.pick>-1) {
				createPickList($td,collegeSurvivorTeams,pick.pick,pick.pickID,pick.picktype);
			}
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
			password: parameters.password,
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

var createPickList = function($td,teamList,selection,pickId,pickType)
{
	var $newPickList = $("<select>",{
		"class" : "pick",
		"data-pickId": pickId,
		"data-pickType" : pickType,
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
