var pages = ["Check-in: Staff", "Check-in: Home", "Check-in: Visitor"];
var leftButton = ['', '< Staff', '< Home'];
var rightButton = ['Home >', 'Visitor >', ''];
var adminPin = '';
function idleTimer() {
	var t;
	window.onload = resetTimer;
	window.onmousemove = resetTimer;
	window.onmousedown = resetTimer;  // catches touchscreen presses as well      
	window.ontouchstart = resetTimer; // catches touchscreen swipes as well 
	window.onclick = resetTimer;      // catches touchpad clicks as well
	window.onkeypress = resetTimer;   
	window.addEventListener('scroll', resetTimer, true);
	
	get("get.php?type=visitor", getVisitors);
	get("get.php?type=staff", getStaff);
	
	window.setInterval(function(){
		if(carousel.getActiveIndex() === 1)
		{
			get("get.php?type=visitor", getVisitors);
			get("get.php?type=staff", getStaff);
		}
	}, 10000);

	function isIdle() {
		carousel.setActiveIndex(1);
		document.getElementById("title").textContent = pages[carousel.getActiveIndex()];
	}

	function resetTimer() {
		clearTimeout(t);
		t = setTimeout(isIdle, 10000);  // time is in milliseconds
	}
}
idleTimer();
function get(theUrl, callback) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.callback = callback;
	xmlHttp.arguments = Array.prototype.slice.call(arguments, 2);
	xmlHttp.onload = xmlSuccess;
	xmlHttp.onerror = xmlError;
    xmlHttp.open("GET", theUrl, true);
    xmlHttp.send(null);
}
function qpost(theUrl, params) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("POST", theUrl, true);
    xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlHttp.send(params);
}
function xmlSuccess() {
	this.callback.apply(this, this.arguments);
}
function xmlError() {
}
function getVisitors() {
	document.getElementById("visitorlist").innerHTML = this.responseText;
}
function getStaff() {
	document.getElementById("stafflist").innerHTML = this.responseText;
}
function next() {
	carousel.next();
}
function prev() {
	carousel.prev();
}
function titles() {
	document.getElementById("title").textContent = pages[carousel.getActiveIndex()];
	document.getElementById("leftBtn").textContent = leftButton[carousel.getActiveIndex()];
	document.getElementById("rightBtn").textContent = rightButton[carousel.getActiveIndex()];
}
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
}
function setStaffState(id, entry) {
	qpost('set.php', 'type=staff&state=' + entry.checked + '&id=' + id);
}
function setVistorState(id, entry) {
	qpost('set.php', 'type=visitor&state=' + entry.checked + '&id=' + id);
}
function adminPanel() {
	// Called on admin panel appear
};
function enterAdmin(_id) {
	if(_id != undefined) {
		post('checkin.php', {id: _id.toString(), pin: adminPin.toString()});
	}
	modalHide('adminpanel');
};
function enterPin(pinNum) {
	if (pinNum >= 0 && pinNum <= 9) {
		adminPin = adminPin + pinNum.toString();
	}
}
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
}
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
}
var adminState = 0;
document.addEventListener('dragdown', function(event) {
	if (event.target.matches('#title') && adminState === 0) {
		adminState = 1;
		modalShow('signoutpanel');
	}
});
document.addEventListener('dragup', function(event) {
	if (adminState === 1) {
		adminState = 2;
		modalHide('signoutpanel');
		modalShow('adminpanel');
	}
});
ons.ready(function() {
	var carousel = document.addEventListener('postchange', function(event) {
		titles();
	});
});
document.querySelector('signoutpanel').onDeviceBackButton = modalHide('signoutpanel');
document.querySelector('adminpanel').onDeviceBackButton = modalHide('adminpanel');
