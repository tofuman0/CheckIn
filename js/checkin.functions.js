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
		if(document.getElementById("addvisitor").visible === false) {
			carousel.setActiveIndex(1);
			document.getElementById("title").textContent = pages[carousel.getActiveIndex()];
		}
	}

	function resetTimer() {
		clearTimeout(t);
		t = setTimeout(isIdle, 10000);  // time is in milliseconds
	}
};
idleTimer();
ons.ready(function() {
	var carousel = document.addEventListener('postchange', function(event) {
		titles();
	});
});
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
};