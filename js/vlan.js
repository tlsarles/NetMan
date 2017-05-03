function addVLAN() {
	var vlanNum = $('#addVLANNum').val();
	var vlanName = $('#addVLANName').val();
	$.post( "classes/query.php", { addVLAN: vlanNum, vlanName: vlanName}, function( data ) {
		location.reload();
	});
}
function addSubnet() {
	var network = $('#addSubnetNW').val();
	var cidr = $('#addSubnetCIDR').val();
	$.post( "classes/query.php", { addSubnet: network, subnetCIDR: cidr}, function( data ) {
		location.reload();
	});
}

function deleteSubnet(subnetName, subnetID) {
	if (confirm('Delete '+subnetName+'? \(ID:'+subnetID+'\)')) {
		$.post( "classes/query.php", { deleteSubnet: subnetID}, function( data ) {
			location.reload(true);
		});
	}
}

function updateNetVLAN(subnetID) {
	var selectedvlan = $('#vlanSelect').val();
	$.post( "classes/query.php", { updateNetVLAN: subnetID, vlan: selectedvlan}, function( data ) {
		location.reload();
	});
}

function vlanEditor(subnetID) {
	$('.modal-title').text("Loading...");
	$('.modal-body').text("Loading...");
	$.post( "classes/query.php", { getSubnet: subnetID}, function( data ) {
		data = JSON.parse(data)[0];
		$('.modal-title').text("Subnet ID:" + data['subnetid'] + " - " + data['subnetip'] + " /" + data['subnetmask']);
		
		$.post( "classes/query.php", { getVLANs: 1}, function( VLANdata ) {
			VLANdata = JSON.parse(VLANdata);
			var output ='<select id="vlanSelect" size="15" style="width:100%;">';
			$.each(VLANdata, function(index, value) {
				output += '<option value="'+value['vlanid']+'"';
				if(value['vlanid'] == data['vlanid']) output += " selected";
				output +=">" + value['vlanid'] + " - " + value['vlanname'] + "</option>";
			});
			output += '</select>';
			$('.modal-body').html(output);
			$('.modal-footer').html('<button type="button" onclick="updateNetVLAN('+subnetID+')" class="btn btn-primary">Update</button>');
		});
		
	});
}

