function parseDate(input) {
	
	var parts = input.split('-');
	
	// YYYY-MM-DDTHH:MM:SSZ
	Date.parse("2005-07-08T00:00:00Z");
}

function getColor(value){
    //value from 0 to 1
	var red = Math.round(value * 255);
	var green = 255 - red;
    return "rgba("+red+","+green+",0,0.5)";
}
/*
function getColor(value){
    //value from 0 to 1
    var hue=((1-value)*120).toString(10);
    return ["hsl(",hue,",100%,50%)"].join("");
}
*/
function getPingColor(pingms) {
	pingms = pingms/1000;
	if(pingms < 0) pingms = 0;
	if(pingms > 1) pingms = 1;
	return getColor(pingms);
}
function deleteDevice(deviceName, deviceID) {
	if (confirm('Delete '+deviceName+'? \(ID:'+deviceID+'\)')) {
		$.post( "classes/query.php", { deleteDevice: 1, devid: deviceID}, function( data ) {
			location.reload(true);
		});
	} else {
		// Do nothing!
	}
}

function addDevice() {
	if(Number.isInteger(thisLocation)) {
		var devIP = $('#devIP').val().trim();
		var devName = $('#devName').val().trim();
		$.post( "classes/query.php", { addDevice: 1, ip: devIP, hostname: devName,location: thisLocation}, function( data ) {
			location.reload(true);
		});
	}
}

function updateDates(minTime, maxTime) {
	// If same date, only show time.
	minTime = new Date(minTime);
	maxTime = new Date(maxTime);
	if(minTime.getDate() == maxTime.getDate() && minTime.getMonth() == maxTime.getMonth() && minTime.getFullYear() == maxTime.getFullYear()) {
		minTime = minTime.getHours()+":"+minTime.getMinutes()+":"+minTime.getSeconds();
		maxTime = maxTime.getHours()+":"+maxTime.getMinutes()+":"+maxTime.getSeconds();
	}
	$('#minTime').text(minTime);
	$('#maxTime').text(maxTime);
}

function loadStore() {
	// GET PERMISSIONS
	$.post( "classes/query.php", { getPrivilege: 1}, function( privilege ) {
	
		// ADD DEVICE PANEL
		if(privilege == "admin") {
			var form = new Form();
			form.addText("devIP", "Device IP");
			form.addText("devName", "Device Name");
			form.addButton("Add Device", "addDevice()");
			var panel = new Panel("Add Device", form.toHTML());
			$('#storeDiv').append(panel.toHTML());
		}
		
		// BUILD INITAL TABLE
		var table = new Table();
		$.each(storeData, function(index, value) {
			var tableValues;
			var removeButton = '<span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="deleteDevice(\''+value['devname']+'\',\''+value['devid']+'\')"></span>';
			if(privilege == "admin") tableValues = {remove:removeButton,name:value['devname'], ip:value['intip']}
			else tableValues = {name:value['devname'], ip:value['intip']};
			table.addRow(value['devid'], tableValues);
		});
		$('#storeDiv').append('<p>From <span id="minTime">X</span> to <span id="maxTime">Y</id></p>');
		$('#storeDiv').append(table.table);
		var minTime = null;
		var maxTime = null;
		// GET PING RESULTS AND FILL OUT TABLE
		$.each(storeData, function(index, value) {
			var thisDevice = value['devid'];
			$.post( "classes/query.php", { getDeviceHistory: 1, devid: thisDevice}, function( data ) {
				data = JSON.parse(data);

				for(var i in data) {
					var td = document.createElement('td');
					var color;
					if(data[i]['up'] == 0) {
						color = "rgba(255,0,0,0.5)";
					} else {
						color = getPingColor(data[i]['pingms']);
					}
					td.style.backgroundColor = color;
					td.style.fontSize = "7pt";
					td.classList.add('pingResult');
					$(td).attr("data-toggle", "tooltip");
					$(td).attr("data-placement", "top");
					$(td).attr("title", data[i]['pingtime']);
					if(minTime == null || Date.parse(data[i]['pingtime']) < minTime) {
						minTime = Date.parse(data[i]['pingtime']);
						updateDates(minTime, maxTime);
					}
					if(maxTime == null || Date.parse(data[i]['pingtime']) > maxTime) {
						maxTime = Date.parse(data[i]['pingtime']);
						updateDates(minTime, maxTime);
					}
					//console.log(new Date(Date.parse(data[i]['pingtime'])));
					var tableData = Math.round(data[i]['pingms']);
					$(td).append(tableData);
					$('#'+thisDevice).append(td);
				}
			});
		});
		$('[data-toggle="tooltip"]').tooltip();
	});
}



$( document ).ready(function() {
    loadStore();
});