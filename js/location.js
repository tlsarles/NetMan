function addLocation() {
	var locName = $('#addLocationTextInput').val();
	$.post( "classes/query.php", { addLocation: locName}, function( data ) {
		location.reload();
	});
}
function deleteLocation(locname,locid) {
	if (confirm('Delete '+locname+'? \(ID:'+locid+'\)')) {
		$.post( "classes/query.php", { deleteLocation: locid}, function( data ) {
			location.reload();
		});
	}
}