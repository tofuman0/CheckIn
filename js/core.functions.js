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
function logout() {
	modalHide('adminpanel');
	post('signin.php', {logout: 'true'});
};
function login() {  
	var _username = document.getElementById('username').value;  
	var _password = document.getElementById('password').value;  
	post('signin.php', {username: _username, password: _password});
};
function createAccount() {  
	var _username = document.getElementById('username').value;  
	var _password = document.getElementById('password').value;  
	var _email = document.getElementById('email').value;  
	var _displayname = document.getElementById('displayname').value;  
	post('create.php', {username: _username, password: _password, email: _email, displayname: _displayname});
};
function exitAdmin() {
	post('checkin.php', {leaveadmin: true});
};
window.fn = {};
window.fn.open = function() {
  var menu = document.getElementById('menu');
  menu.open();
};
window.fn.load = function(page) {
  var content = document.getElementById('content');
  var menu = document.getElementById('menu');
  content.load(page)
    .then(menu.close.bind(menu));
  if(page === 'admstaff.html') {
	get("get.php?type=admstaff", getAdmStaff);
  }
  else if(page === 'admvisitor.html') {
	get("get.php?type=admvisitor", getAdmVisitors);
  }
};