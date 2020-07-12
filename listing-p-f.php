<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.3.x                          //
//                        By John Mordo jlm69 at Xoops                       //
//                         from the myAds 2.04 Module                        //
//                    All Original credits left below this                   //
//                                                                           //
//                                                                           //
//                                                                           //
//                                                                           //
// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller                                    //
// Author Website : pascal.e-xoops@perso-search.com                          //
// Licence Type   : GPL                                                      //
// ------------------------------------------------------------------------- //
include("header.php");

$mydirname = basename( dirname( __FILE__ ) ) ;
require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;
include(XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");

function EnvAnn($lid)
{
    global $xoopsConfig, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;
	
	$result = $xoopsDB->query("select lid, name, mname, lname, school, year, studies, activities, occ, date, email, submitter, town, photo, photo2  FROM ".$xoopsDB->prefix("alumni_listing")." where lid= ".mysql_real_escape_string($lid)."");
    list($lid, $name, $mname, $lname, $school, $year, $studies, $activities, $occ, $date, $email, $submitter, $town, $photo, $photo2) = $xoopsDB->fetchRow($result);
	
    OpenTable();
    echo "
	    <b>"._ALUMNI_SENDTO." $lid \"<b>$name $mname $lname </b>\" "._ALUMNI_FRIEND."<br /><br />
	    <form action=\"listing-p-f.php\" method=\"post\">
	    <input type=\"hidden\" name=\"lid\" value=$lid>";
    if($xoopsUser) {
		$idd =$iddds =$xoopsUser->getVar("name", "E");
		$idde =$iddds =$xoopsUser->getVar("email", "E");
	}
    echo "
	<table width='100%' class='outer' cellspacing='1'>
    <tr>
      <td class='head' width='30%'>"._ALUMNI_NAME." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"yname\" value=\"$idd\"></td>
    </tr>
    <tr>
      <td class='head'>"._ALUMNI_MAIL." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"ymail\" value=\"$idde\"></td>
    </tr>
    <tr>
      <td colspan=2 class='even'>&nbsp;</td>
    </tr>
    <tr>
      <td class='head'>"._ALUMNI_NAMEFR." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"fname\"></td>
    </tr>
    <tr>
      <td class='head'>"._ALUMNI_MAILFR." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"fmail\"></td>
    </tr>
	</table><br />
    <input type=\"hidden\" name=\"op\" value=\"MailAnn\" />
    <input type=\"submit\" value="._ALUMNI_SENDFR." />
    </form>     ";
    CloseTable();
}


function MailAnn($lid, $yname, $ymail, $fname, $fmail)
{
    global $xoopsConfig, $xoopsUser, $xoopsDB, $myts, $xoopsLogger, $mydirname;
	
	$result = $xoopsDB->query("select lid, name, mname, lname, school, year, studies, activities, occ, date, email, submitter, town, photo, photo2 FROM ".$xoopsDB->prefix("alumni_listing")." where lid= ".mysql_real_escape_string($lid)."");
    list($lid, $name, $mname, $lname, $school, $year, $studies, $activities, $occ, $date, $email, $submitter, $town, $photo, $photo2) = $xoopsDB->fetchRow($result);
	
    
    
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

	$subject = ""._ALUMNI_SUBJET." ".$xoopsConfig['sitename']."";
	$message .= ""._ALUMNI_HELLO." $fname,\n\n$yname "._ALUMNI_MESSAGE."\n\n";
	$message .= " $name\n $mname\n $lname\n";   
	$message .= " $school\n$year\n";
	if ($studies) {
	$message .= ""._ALUMNI_STUDIES." $studies\n";
	}
    $message .= ""._ALUMNI_BYMAIL." ".XOOPS_URL."/modules/$mydirname/index.php?pa=viewlistings&amp;lid=$lid\n";
	if ($tel) {
		$message .= ""._ALUMNI_TEL2." $tel\n";
	}
	if ($town) {
		$message .= ""._ALUMNI_TOWN." $town\n";
	}
	
	$message .= "\n"._ALUMNI_INTERESS." ".$xoopsConfig['sitename']."\n".XOOPS_URL."/modules/$mydirname/\n\n";
	$mail =& xoops_getMailer();
	$mail->useMail();
	$mail->setFromEmail($ymail);
	$mail->setToEmails($fmail);
	$mail->setSubject($subject);
	$mail->setBody($message);
	$mail->send();
	echo $mail->getErrors();
	
	redirect_header("index.php",1,_ALUMNI_ALUMEND);
	exit();
}

function ImprAnn($lid)
{
	//global $xoopsConfig, $xoopsDB, $useroffset, $myts,$xoopsLogger;
    global $xoopsConfig, $xoopsUser, $xoopsDB, $useroffset, $myts, $xoopsLogger, $mydirname;
	
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
		echo "<tr><td><b>"._ALUMNI_TOWN."</b><div style=\"text-align:justify;\">$town</div><p>";     
	echo "</td>
	      </tr>";
	}
    
	if ($photo) {
		echo "<tr><td><b>"._ALUMNI_GPHOTO."</b><br /><br /><img src=\"grad_photo/$photo\" border=0 valign=top></td>";
	}
	
	if ($photo2) {
		echo "<td><b>"._ALUMNI_IMG2."</b><br /><br /><img src=\"now_photo/$photo2\" border=0 valign=top></td></tr>";
	}

	echo "<br />"._ALUMNI_DATE2." $date <br />";
	echo "</td>
	</tr>
	</table>";
	echo "</td></tr></table></td></tr></table>
    <br /><br /><center>
    "._ALUMNI_EXTRANN." <b>".$xoopsConfig['sitename']."</b><br />
    <a href=\"".XOOPS_URL."/modules/$mydirname/\">".XOOPS_URL."/modules/$mydirname/</a>
    </td></tr></table>
    </body>
    </html>";
}


##############################################################


$yname = !empty($_POST['yname']) ? $myts->htmlSpecialChars($_POST['yname']) : "";
$ymail = !empty($_POST['ymail']) ? $myts->htmlSpecialChars($_POST['ymail']) : "";
$fname = !empty($_POST['fname']) ? $myts->htmlSpecialChars($_POST['fname']) : "";
$fmail = !empty($_POST['fmail']) ? $myts->htmlSpecialChars($_POST['fmail']) : "";

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

    case "EnvAnn":
	include(XOOPS_ROOT_PATH."/header.php");
	EnvAnn($lid);
	include(XOOPS_ROOT_PATH."/footer.php");
	break;
	
    case "MailAnn":
	MailAnn($lid, $yname, $ymail, $fname, $fmail);
	break;
	
    case "ImprAnn":
	ImprAnn($lid);
	break;

    default:
	redirect_header("index.php",1,""._RETURNGLO."");
	break;

}

?>