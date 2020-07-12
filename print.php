<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.3.x                             //
//                             By John Mordo                                 //
//                                                                           //
//                                                                           //
//                                                                           //
//                                                                           //
// ------------------------------------------------------------------------- //

include("header.php");

$mydirname = basename( dirname( __FILE__ ) ) ;
require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;
include(XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");

function PrintAlum($lid=0)
{

    global $xoopsConfig, $xoopsUser, $xoopsDB, $xoopsModuleConfig, $useroffset, $myts, $xoopsLogger, $mydirname, $main_lang;
	
	$currenttheme = $xoopsConfig['theme_set'];
	
	$result = $xoopsDB->query("select lid, cid, name, mname, lname, school, year, studies, activities, occ, date, email, submitter, town, photo, photo2 FROM ".$xoopsDB->prefix("alumni_listing")." where lid= ".mysql_real_escape_string($lid)."");
    list($lid, $cid, $name, $mname, $lname, $school, $year, $studies, $activities, $occ, $date, $email, $submitter, $town, $photo, $photo2) = $xoopsDB->fetchRow($result);

	$name = $myts->htmlSpecialChars($name);
	$mname = $myts->htmlSpecialChars($mname);
	$lname = $myts->htmlSpecialChars($lname);
	$school = $myts->htmlSpecialChars($school);
	$year = $myts->htmlSpecialChars($year);
	$studies = $myts->htmlSpecialChars($studies);
	$activities = $myts->displayTarea($activities,1,0,1,1,1);
	$occ = $myts->htmlSpecialChars($occ);
	$submitter = $myts->htmlSpecialChars($submitter);
	$town = $myts->htmlSpecialChars($town);
	
	echo "
    <html>
    <head><title>".$xoopsConfig['sitename']."</title>
	<link rel=\"StyleSheet\" href=\"../../themes/".$currenttheme."/style/style.css\" type=\"text/css\">
	</head>
    <body bgcolor=\"#FFFFFF\" text=\"#000000\">
    <table border=0><tr><td>
    <table border=0 width=640 cellpadding=0 cellspacing=1 bgcolor=\"#000000\"><tr><td>
    <table border=0 width=100% cellpadding=8 cellspacing=1 bgcolor=\"#FFFFFF\"><tr><td>";
	$useroffset = "";
    if($xoopsUser) {
		$timezone = $xoopsUser->timezone();
		if(isset($timezone)){
			$useroffset = $xoopsUser->timezone();
		}else{
			$useroffset = $xoopsConfig['default_TZ'];
		}
	}
	//$date = ($useroffset*3600) + $date;	
	$date = formatTimestamp($date,"s");
	echo "<table width=100% border=0 valign=top><tr><td><b>$name&nbsp;$mname&nbsp;$lname<br /> $school "._ALUMNI_CLASSOF." $year</b>";
	echo "</td>
	      </tr>";

	if ($studies) {
    echo "<tr>
      <td><b>"._ALUMNI_STUDIES."</b><div style=\"text-align:justify;\">$studies</div><p>";
      echo "</td>
	      </tr>";
	      }

	      if ($activities) { 
	      echo "<tr><td><b>"._ALUMNI_ACTIVITIES."</b><div style=\"text-align:justify;\">$activities</div><p>";
	      echo "</td>
	      </tr>";
	      }

	      if ($occ) {
	      echo "<tr><td><b>"._ALUMNI_OCC."</b><div style=\"text-align:justify;\">$occ</div><p>";     
	echo "</td>
	      </tr>";
	      }

	if ($town) {
	echo "<tr><td><b>"._ALUMNI_TOWN."</b><div style=\"text-align:justify;\">$town</div>";
	echo "</td></tr>";
	}
echo "</table><table width=\"100%\" border=0 valign=top>";
	if ($photo) {
		echo "<tr><td width=\"40%\" valign=\"top\"><b>"._ALUMNI_GPHOTO."</b><br /><br /><img src=\"grad_photo/$photo\" border=0></td>";
	}

	if ($photo2) {
		echo "<td width=\"60%\" valign=\"top\"><b>"._ALUMNI_RPHOTO."</b><br /><br />&nbsp;&nbsp;&nbsp;<img src=\"now_photo/$photo2\" border=0></td></tr>";
	}
echo "</table><table border=0>";
	echo "<tr><td><b>"._ALUMNI_DATE2." $date <br />";
	echo "</td>
	</tr></table>
</td></tr></table></td></tr></table>
    <br /><br /><center>
    "._ALUMNI_EXTRANN." <b>".$xoopsConfig['sitename']."</b><br />
    <a href=\"".XOOPS_URL."/modules/$mydirname/\">".XOOPS_URL."/modules/$mydirname/</a>
    </td></tr></table>
    </body>
    </html>";
}

##############################################################

if(!isset($_POST['lid']) && isset($_GET['lid']) ) {
	$lid = intval($_GET['lid']) ;
}else {
	$lid = intval($_POST['lid']) ;
}

$op= '';
if (!empty($_GET['op'])) {
	$op = $_GET['op'];
} elseif (!empty($_POST['op'])) {
	$op = $_POST['op'];
}

switch($op) {
	
    case "PrintAlum":
	PrintAlum($lid);
	break;

    default:
	redirect_header("index.php",3,""._RETURNGLO."");
	break;

}

?>