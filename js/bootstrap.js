var DropDown = function(ddName) {
	this.dropDown = document.createElement('li');
	this.dropDown.classList.add('dropdown');
	var dropDownButton = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'+ddName+' <span class="caret"></span></a>';
	this.ul = document.createElement('ul');
	this.ul.classList.add('dropdown-menu');
	$(this.dropDown).append(dropDownButton);
	$(this.dropDown).append(this.ul);
	$('.navbar-nav').append(this.dropDown);
}

DropDown.prototype.addItem = function(name, link) {
	var li = document.createElement('li');
	var a = document.createElement('a');
	a.href = link;
	a.appendChild(document.createTextNode(name));
	li.appendChild(a);
	this.ul.appendChild(li);
}

var Table = function () {
	this.table = document.createElement('table');
	this.table.classList.add('table');
	this.table.classList.add('table-striped');
	this.tbody = document.createElement('tbody');
	this.table.appendChild(this.tbody);
};

Table.prototype.addRow = function(rowID, input) {
	var tr = document.createElement('tr');
	tr.id = rowID;
	$.each(input, function(index, value) {
		var td = document.createElement('td');
		td.id = index;
		//td.appendChild(document.createTextNode(value));
		$(td).append(value);
		tr.appendChild(td);
	});
	this.tbody.appendChild(tr);
};

var Form = function() {
	this.data = '';
}
Form.prototype.addText = function(id = '', placeholder = '') {
	this.data += `<input type="text" id="${id}" placeholder="${placeholder}" class="form-control"> `;
}
Form.prototype.addButton = function(btnTxt, btnClick = '') {
	this.data += `<button type="button" onclick="${btnClick}" class="btn btn-primary">${btnTxt}</button>`;
}
Form.prototype.toHTML = function() {
	var output = '<form class="form-inline">';
	output += this.data;
	output += '</form>';
	return output;
}

var Panel = function(heading, body, type = "primary") {
		this.heading = heading;
		this.body = body;
		this.type = type;
}
Panel.prototype.toHTML = function() {
	$output = "<div class=\"panel panel-"+this.type+"\">";
	$output += "<div class=\"panel-heading\"><h3 class=\"panel-title\">"+this.heading+"</h3></div>";
	$output += "<div class=\"panel-body\">"+this.body+"</div></div>";
	return $output;
}

// Builds the store options for the navbar
function storeNav() {
	var counter = 0;
	var dd;
	$.post( "http://"+window.location.hostname+"/classes/query.php", { getStoreList: 1}, function( data ) {
			data = JSON.parse(data);
			for(var i in data) {
				if(counter % 25 == 0) {
					dd = new DropDown('Locations');
				}
				dd.addItem(data[i]['locname'], "store.php?location="+data[i]['locid']);				
				counter++;
			}
			var settings = new DropDown('Configure');
			settings.addItem('Alerts', 'alerts.php');
			settings.addItem('Networks', 'vlans.php');
			settings.addItem('Locations', 'location.php');
	});
}

$( document ).ready(function() {
    storeNav();
});