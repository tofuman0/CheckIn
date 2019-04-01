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
		echo "<ul class=\"list\" style=\"overflow: auto; max-height: calc(100% - 50px)\">";
		for ($x = 0; $x < count($result); $x++) {
			echo "
			<li class=\"list-item\">
				<div class=\"list-item__center\">
					<div class=\"list-item__title\">{$result[$x]['firstname']} {$result[$x]['lastname']}</div>
					<div class=\"list-item__subtitle\">{$result[$x]['company']}</div>
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
				<ons-toolbar-button>Add Visitor</ons-toolbar-button>
			</div>";
	}
	else if($_GET['type'] == "staff")
	{
		$result = getStaff($SITEID);
		if($result == null)
		{
			goto staffskip;
		}
		echo "<ul class=\"list\" style=\"overflow: auto; max-height: 100%\">";
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
				echo "<img class=\"list-item__thumbnail\" src=\"\\user.png\" alt=\"...\">";
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
}
?>