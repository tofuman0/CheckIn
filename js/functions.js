var admType = 0;
var admID = 0;
function get(theUrl, callback) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.callback = callback;
	xmlHttp.arguments = Array.prototype.slice.call(arguments, 2);
	xmlHttp.onload = xmlSuccess;
	xmlHttp.onerror = xmlError;
    xmlHttp.open("GET", theUrl, true);
    xmlHttp.send(null);
};
function qget(theUrl) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", theUrl, true);
    xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlHttp.send();
};
function qpost(theUrl, params) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("POST", theUrl, true);
    xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlHttp.send(params);
};
function xmlSuccess() {
	this.callback.apply(this, this.arguments);
};
function xmlError() {
};
function getVisitors() {
	document.getElementById("visitorlist").innerHTML = this.responseText;
};
function getStaff() {
	document.getElementById("stafflist").innerHTML = this.responseText;
};
function getAdmVisitors() {
	if(this.responseText != '') {
		document.getElementById("admvisitor.html").innerHTML = this.responseText;
	}
	else {
		post('checkin.php', {});
	}
};
function getAdmStaff() {
	if(this.responseText != '') {
		document.getElementById("admstaff.html").innerHTML = this.responseText;
	}
	else {
		post('checkin.php', {});
	}
};
function admExtend() {
	qget("get.php?type=admextend");
}
function next() {
	carousel.next();
};
function prev() {
	carousel.prev();
};
function titles() {
	document.getElementById("title").textContent = pages[carousel.getActiveIndex()];
	document.getElementById("leftBtn").textContent = leftButton[carousel.getActiveIndex()];
	document.getElementById("rightBtn").textContent = rightButton[carousel.getActiveIndex()];
};
function post(path, params) {
	var form = document.createElement("form");
	form.setAttribute("method", "post");
	form.setAttribute("action", path);

	for(var key in params) {
		if(params.hasOwnProperty(key)) {
			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("type", "hidden");
			hiddenField.setAttribute("name", key);
			hiddenField.setAttribute("value", params[key]);
			form.appendChild(hiddenField);
		}
	}

	document.body.appendChild(form);
	form.submit();
};
function setStaffState(id, entry) {
	qpost('set.php', 'type=staff&state=' + entry.checked + '&id=' + id);
};
function setVistorState(id, entry) {
	qpost('set.php', 'type=visitor&state=' + entry.checked + '&id=' + id);
};
function addVisitor(siteid) {
	if(document.getElementById("va-Firstname").value != '' && document.getElementById("va-Lastname").value != '') {
		qpost('set.php', 'type=addvisitor' +
			'&title=' + document.getElementById("va-Title").value +
			'&fn=' + document.getElementById("va-Firstname").value +
			'&ln=' + document.getElementById("va-Lastname").value +
			'&cm=' + document.getElementById("va-Company").value +
			'&rg=' + document.getElementById("va-Reg").value +
			'&sid=' + siteid
		);
		closeAddVisitor();
		get("get.php?type=visitor", getVisitors);
	}
	else {
		alert("First and last names are required to create a visitor.");
	}
};
function addAdmVisitor(siteid) {
	if(document.getElementById("va-Firstname").value != '' && document.getElementById("va-Lastname").value != '') {
		qpost('set.php', 'type=addvisitor' +
			'&title=' + document.getElementById("va-Title").value +
			'&fn=' + document.getElementById("va-Firstname").value +
			'&ln=' + document.getElementById("va-Lastname").value +
			'&cm=' + document.getElementById("va-Company").value +
			'&rg=' + document.getElementById("va-Reg").value +
			'&sid=' + siteid
		);
		closeAddVisitor();
		get("get.php?type=admvisitor", getAdmVisitors);
	}
	else {
		alert("First and last names are required to create a visitor.");
	}
};
function addStaff(siteid) {
	if(document.getElementById("sa-Firstname").value != '' && document.getElementById("sa-Lastname").value != '') {
		qpost('set.php', 'type=addstaff' +
			'&title=' + document.getElementById("sa-Title").value +
			'&fn=' + document.getElementById("sa-Firstname").value +
			'&ln=' + document.getElementById("sa-Lastname").value +
			'&cm=' + document.getElementById("sa-Company").value +
			'&rg=' + document.getElementById("sa-Reg").value +
			'&sid=' + siteid
		);
		closeAddVisitor();
		get("get.php?type=staff", getVisitors);
	}
	else {
		alert("First and last names are required to create a visitor.");
	}
};
function closeAddVisitor() {
	document.getElementById("va-Title").value = "";
	document.getElementById("va-Firstname").value = "";
	document.getElementById("va-Lastname").value = "";
	document.getElementById("va-Company").value = "";
	document.getElementById("va-Reg").value = "";
	hideDialog('addvisitor');
	admExtend();
};
function closeAddStaff() {
	document.getElementById("sa-Title").value = "";
	document.getElementById("sa-Firstname").value = "";
	document.getElementById("sa-Lastname").value = "";
	document.getElementById("sa-Company").value = "";
	document.getElementById("sa-Reg").value = "";
	hideDialog('addstaff');
	admExtend();
};
function admConfirmClose() {
	document
		.getElementById('adm-alert-dialog')
		.hide();
	admExtend();
};
function admConfirmDelete() {
	if(admType === 0) {
		qpost('set.php', 'type=admdeletevisitor' +
				'&id=' + admID
			);
		var elem = document.getElementById("admVisitor_" + admID);
		elem.parentElement.removeChild(elem);
	} else {
		qpost('set.php', 'type=admdeletestaff' +
				'&id=' + admID
			);
		var elem = document.getElementById("admStaff_" + admID);
		elem.parentElement.removeChild(elem);
	}
	admConfirmClose();
}
function admAddVisitor() {
	admExtend();
	showDialog('addvisitor');
};
function admAddStaff() {
	admExtend();
	showDialog('addstaff');
};
function admDeleteVisitor(id) {
	admType = 0;
	admID = id;
	var dialog = document.getElementById('adm-alert-dialog');

	if (dialog) {
		dialog.show();
	} else {
		ons.createElement('alert-dialog.html', { append: true })
		.then(function(dialog) {
			dialog.show();
		});
	}
	admExtend();
};
function admDeleteStaff(id) {
	admType = 1;
	admID = id;
	var dialog = document.getElementById('adm-alert-dialog');

	if (dialog) {
		dialog.show();
	} else {
		ons.createElement('alert-dialog.html', { append: true })
		.then(function(dialog) {
			dialog.show();
		});
	}
	admExtend();
};
function showDialog(id) {
  document
    .getElementById(id)
    .show();
};
function hideDialog(id) {
  document
    .getElementById(id)
    .hide();
};
function modalShow(type) {
	var modal = document.getElementById(type);
	if(modal != null)
	{
		var options = {
			animation: 'fade',
			animationOptions: {
				duration: 0.5,
				delay: 0.0,
				timing: 'ease-in'
			},
			callback: adminPanel
		};
		modal.show(options);
	}
};
function modalHide(type) {
	var modal = document.getElementById(type);
	if(modal != null)
	{
		var options = {
			animation: 'fade',
			animationOptions: {
				duration: 0.5,
				delay: 0.0,
				timing: 'ease-out'
			}
		};
		modal.hide(options);
		adminState = 0;
		adminPin = '';
	}
};


