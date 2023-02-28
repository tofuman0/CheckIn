<?php
require_once('config.php');
require_once('functions.php');

$result = checkToken();
if(!isset($result))
{
	die();
}

$result = getSiteDetails();
$SITEID = $result['id'];
if(isset($_GET['type']))
{
	if($_GET['type'] == "visitor")
	{
		$result = getVisitor($SITEID);
		if($result == null)
		{
			goto visitorskip;
		}
		echo "<ul class=\"list\" style=\"overflow: none; max-height: calc(100% - 50px)\">";
		for ($x = 0; $x < count($result); $x++) {
			$reg = "";
			if(strlen($result[$x]['company']) > 0 && strlen($result[$x]['vehiclereg']) > 0) $reg = "- ";
			if(strlen($result[$x]['vehiclereg']) > 0) $reg .= "<i>" . $result[$x]['vehiclereg'] . "</i>";
			echo "
			<li class=\"list-item\">
				<div class=\"list-item__center\">
					<div class=\"list-item__title\">{$result[$x]['firstname']} {$result[$x]['lastname']}</div>
					<div class=\"list-item__subtitle\">{$result[$x]['company']} {$reg}</div>
				</div>
				<div class=\"list-item__right\">";
				if($result[$x]['signedin'] == '1')
				{
					echo "<ons-switch checked class='user-switch' id='v{$result[$x]['id']}' onClick='setVistorState({$result[$x]['id']}, this)'></ons-switch>";
				}
				else
				{
					echo "<ons-switch class='user-switch' id='v{$result[$x]['id']}' onClick='setVistorState({$result[$x]['id']}, this)'></ons-switch>";
				}
				echo "</div>
			</li>
			";
		}
		echo "</ul>";
		visitorskip:
		echo "<div class=\"tabbar\">
				<ons-toolbar-button onClick=\"showDialog('addvisitor')\">Add Visitor</ons-toolbar-button>
			</div>";
	}
	else if($_GET['type'] == "staff")
	{
		$result = getStaff($SITEID);
		if($result == null)
		{
			goto staffskip;
		}
		echo "<ul class=\"list\" style=\"overflow: none; max-height: 100%\">";
		for ($x = 0; $x < count($result); $x++) {
			echo "
			<li class=\"list-item\">
				<div class=\"list-item__left\">
				";
			if($result[$x]['thumbnail'] != null)
			{
				$STAFFTB = base64_encode($result[$x]['thumbnail']);
				echo "<img class=\"list-item__thumbnail\" src=\"data:image/png;base64,$STAFFTB\">";
			}
			else
			{
				$FIRSTNAME = $result[$x]['firstname'];
				$LASTNAME = $result[$x]['lastname'];
				$NAME = "$FIRSTNAME $LASTNAME";
				$INITIALS = getCapitals($NAME);
				$BACKCOLOUR = getColor($NAME);
				echo "<svg class=\"list-item__thumbnail\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\" style=\"display:block;\"><circle r=\"50\" cy=\"50\" cx=\"50\" style=\"fill:$BACKCOLOUR\"></circle><text x=\"50\" y=\"50\" dominant-baseline=\"central\" text-anchor=\"middle\" style=\"font-size:45px;fill:white;\">$INITIALS</text></svg>";
			}
			echo "</div>
				<div class=\"list-item__center\">
					<div class=\"list-item__title\">{$result[$x]['firstname']} {$result[$x]['lastname']}</div>
					<div class=\"list-item__subtitle\">{$result[$x]['jobtitle']}</div>
				</div>
				<div class=\"list-item__right\">";
				if($result[$x]['signedin'] == '1')
				{
					echo "<ons-switch checked id='s{$result[$x]['id']}' onClick='setStaffState({$result[$x]['id']}, this)'></ons-switch>";
				}
				else
				{
					echo "<ons-switch id='s{$result[$x]['id']}' onClick='setStaffState({$result[$x]['id']}, this)'></ons-switch>";
				}
				echo "</div>
			</li>
			";
		}
		echo "</ul>";
		staffskip:
	}
	else if($_GET['type'] == "admstaff")
	{
		session_start();
		global $siteadmin_timeout;
		if(!isset($_SESSION['adminaccess']) || $_SESSION['adminaccess'] != 1 || $_SESSION['admintimeout'] < time())
		{
			$_SESSION = array();
			session_unset();
			session_destroy();
			session_write_close();
			die();
		}
		$_SESSION['admintimeout'] = time() + $siteadmin_timeout;
		session_write_close();
		
		if(isset($_SESSION['adminsiteid']))
		{
			echo "<ons-page id=\"admstafflist\">
			<ons-toolbar>
				<div class=\"left\">
					<ons-toolbar-button onclick=\"fn.open()\">
					<ons-icon icon=\"md-menu\"></ons-icon>
					</ons-toolbar-button>
				</div>
				<div class=\"center\">Check-in: Admin - Staff</div>
				<div class=\"right\">
					<ons-button modifier=\"quiet\" onclick=\"admAddStaff()\" style=\"margin: 6px; padding: 0px 6px 0px 6px;\">Add Staff</ons-button>
					<ons-button modifier=\"quiet\" onclick=\"exitAdmin()\" style=\"margin: 6px; padding: 0px 6px 0px 6px;\">Leave</ons-button>
				</div>
			</ons-toolbar>";
			$result = getStaff($_SESSION['adminsiteid']);
			if($result != null)
			{
				for($x = 0; $x < count($result); $x++)
				{
					echo "<ons-list-item class=\"admItem\" id=\"admStaff_{$result[$x]['id']}\" tappable>";
					if($result[$x]['thumbnail'] != null)
					{
						$STAFFTB = base64_encode($result[$x]['thumbnail']);
						echo "<div class=\"left\">
							<img class=\"list-item__thumbnail\" src=\"data:image/png;base64,$STAFFTB\">
						</div>";
					}
					else
					{
						$FIRSTNAME = $result[$x]['firstname'];
						$LASTNAME = $result[$x]['lastname'];
						$NAME = "$FIRSTNAME $LASTNAME";
						$INITIALS = getCapitals($NAME);
						$BACKCOLOUR = getColor($NAME);
						echo "<div class=\"left\">
							<svg class=\"list-item__thumbnail\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\" style=\"display:block;\"><circle r=\"50\" cy=\"50\" cx=\"50\" style=\"fill:$BACKCOLOUR\"></circle><text x=\"50\" y=\"50\" dominant-baseline=\"central\" text-anchor=\"middle\" style=\"font-size:45px;fill:white;\">$INITIALS</text></svg>
						</div>";
					}
					echo "<div class=\"center\">
							<span class=\"list-item__title\">{$result[$x]['firstname']} {$result[$x]['lastname']}</span>
							<span class=\"list-item__subtitle\">{$result[$x]['jobtitle']}</span>
						</div>
						<div class=\"right\">
							<ons-button modifier=\"quiet\" class=\"admBtn\" onclick=\"admEditStaff({$result[$x]['id']})\">Edit</ons-button>
							<ons-button modifier=\"quiet\" class=\"admBtn\" onclick=\"admDeleteStaff({$result[$x]['id']})\">Delete</ons-button>
						</div>
						</ons-list-item>";
				}
			}
			else
			{
				echo "<h3 id=\"admStaffMessage\" class=\"message\">No staff.</h3>";
			}
			echo "</ons-page>";
		}
		else
		{
			echo "<div class=\"center\"><h2>Error loading site data.</h2></div>";
		}
	}
	else if($_GET['type'] == "admvisitor")
	{
		session_start();
		global $siteadmin_timeout;
		if(!isset($_SESSION['adminaccess']) || $_SESSION['adminaccess'] != 1 || $_SESSION['admintimeout'] < time())
		{
			$_SESSION = array();
			session_unset();
			session_destroy();
			session_write_close();
			die();
		}
		$_SESSION['admintimeout'] = time() + $siteadmin_timeout;
		session_write_close();
		
		if(isset($_SESSION['adminsiteid']))
		{
			$result = getVisitor($_SESSION['adminsiteid']);
			if($result != null)
			{
				echo "<ons-page id=\"admvisitorlist\">
					<ons-toolbar>
						<div class=\"left\">
							<ons-toolbar-button onclick=\"fn.open()\">
							<ons-icon icon=\"md-menu\"></ons-icon>
							</ons-toolbar-button>
						</div>
						<div class=\"center\">Check-in: Admin - Visitors</div>
						<div class=\"right\">
							<ons-button modifier=\"quiet\" onclick=\"admAddVisitor()\" style=\"margin: 6px; padding: 0px 6px 0px 6px;\">Add Visitor</ons-button>
							<ons-button modifier=\"quiet\" onclick=\"exitAdmin()\" style=\"margin: 6px; padding: 0px 6px 0px 6px;\">Leave</ons-button>
						</div>
					</ons-toolbar>";
					
				for($x = 0; $x < count($result); $x++)
				{
					echo "<ons-list-item class=\"admItem\" id=\"admVisitor_{$result[$x]['id']}\" tappable>
					<div class=\"center\">
						<span class=\"list-item__title\">{$result[$x]['firstname']} {$result[$x]['lastname']}</span>
						<span class=\"list-item__subtitle\">{$result[$x]['company']} {$result[$x]['vehiclereg']}</span>
					</div>
					<div class=\"right\">
							<ons-button modifier=\"quiet\" class=\"admBtn\" onclick=\"admEditVisitor({$result[$x]['id']})\">Edit</ons-button>
							<ons-button modifier=\"quiet\" class=\"admBtn\" onclick=\"admDeleteVisitor({$result[$x]['id']})\">Delete</ons-button>
					</div>
					</ons-list-item>";
				}
				echo "</ons-page>";
			}
			else
			{
				echo "<h3 id=\"admVisitorMessage\" class=\"message\">No visitors.</h3>";
			}
		}
		else
		{
			echo "<div class=\"center\"><h2>Error loading site data.</h2></div>";
		}
	}
	else if($_GET['type'] == "admextend")
	{
		session_start();
		global $siteadmin_timeout;
		if(!isset($_SESSION['adminaccess']) || $_SESSION['adminaccess'] != 1 || $_SESSION['admintimeout'] < time())
		{
			$_SESSION = array();
			session_unset();
			session_destroy();
			session_write_close();
			die();
		}
		$_SESSION['admintimeout'] = time() + $siteadmin_timeout;
		session_write_close();
	}
}
?>