<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.3.x                          //
//                        By John Mordo jlm69 at Xoops                       //
//                   Originally from the myAds 2.04 Module                   //
//                    All Original credits left below this                   //
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
$main_lang =  '_' . strtoupper( $mydirname ) ;
require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;
include(XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");

function SendFriend($lid=0)
{

   global $xoopsConfig, $xoopsModuleConfig, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger, $mydirname, $main_lang;
	
	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

	$result = $xoopsDB->query("select lid, name, mname, lname, school, year, studies, activities, occ, date, email, submitter, town, photo, photo2  FROM ".$xoopsDB->prefix("".$mydirname."_listing")." where lid= ".mysql_real_escape_string($lid)."");
    list($lid, $name, $mname, $lname, $school, $year, $studies, $activities, $occ, $date, $email, $submitter, $town, $photo, $photo2) = $xoopsDB->fetchRow($result);
	
    	echo "<table width='100%' border='0' cellspacing='1' cellpadding='8'><tr class='bg4'><td valign='top'>\n";
    echo "
	    <b>".constant($main_lang."_SENDTO")." $lid \"<b>$name $mname $lname </b>\" ".constant($main_lang."_FRIEND")."<br /><br />
	    <form action=\"sendfriend.php\" method=\"post\">
	    <input type=\"hidden\" name=\"lid\" value=$lid>";
    if($xoopsUser) {
		$idd =$iddds =$xoopsUser->getVar("name", "E");
		$idde =$iddds =$xoopsUser->getVar("email", "E");
	}
    echo "
	<table width='100%' class='outer' cellspacing='1'>
    <tr>
      <td class='head' width='30%'>".constant($main_lang."_NAME")." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"yname\" value=\"$idd\"></td>
    </tr>
    <tr>
      <td class='head'>".constant($main_lang."_MAIL")." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"ymail\" value=\"$idde\"></td>
    </tr>
    <tr>
      <td colspan=2 class='even'>&nbsp;</td>
    </tr>
    <tr>
      <td class='head'>".constant($main_lang."_NAMEFR")." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"fname\"></td>
    </tr>
    <tr>
      <td class='head'>".constant($main_lang."_MAILFR")." </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"fmail\"></td>
    </tr>";
if ($xoopsModuleConfig["".$mydirname."_use_captcha"] == '1') {
echo "<tr><td class='head'>".constant($main_lang."_CAPTCHA")." </td><td class='even'>";
	$jlm_captcha = "";
	$jlm_captcha = (new XoopsFormCaptcha(constant($main_lang."_CAPTCHA"), "xoopscaptcha", false));
	echo $jlm_captcha->render();
echo "</td></tr>";
}
	echo "</table><br />
    <input type=\"hidden\" name=\"op\" value=\"MailAlum\" />
    <input type=\"submit\" value=".constant($main_lang."_SENDFR")." />
    </form>     ";
    echo "</td></tr></table>";
}


function MailAlum($lid=0, $yname='', $ymail='', $fname='', $fmail='')
{
    global $xoopsConfig, $xoopsUser, $xoopsTpl, $xoopsDB, $xoopsModule, $xoopsModuleConfig, $myts, $xoopsLogger, $mydirname, $main_lang;
	
if ($xoopsModuleConfig["".$mydirname."_use_captcha"] == '1') {
	xoops_load("captcha");
	$xoopsCaptcha = XoopsCaptcha::getInstance();
	if( !$xoopsCaptcha->verify() ) {
        redirect_header( XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/index.php", 2, $xoopsCaptcha->getMessage() );
		}
	}

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

	$tags = array();
	$tags['YNAME'] = stripslashes($yname);
	$tags['YMAIL'] = $ymail;
	$tags['FNAME'] = stripslashes($fname);
	$tags['FMAIL'] = $fmail;
	$tags['HELLO'] = constant($main_lang."_HELLO");
	$tags['LID'] = $lid;
	$tags['CLASSOF'] = constant($main_lang."_CLASSOF");
	$tags['NAME'] = $name;
	$tags['MNAME'] = $mname;
	$tags['LNAME'] = $lname;
	$tags['SCHOOL'] = $school;
	$tags['STUDIES'] = $studies;
	$tags['TOWN'] = $town;
	$tags['YEAR'] = $year;
	$tags['OTHER'] = "".constant($main_lang."_INTERESS")."&nbsp;". $xoopsConfig['sitename']."";
	$tags['LISTINGS'] = "".XOOPS_URL."/modules/$mydirname/";
	$tags['LINK_URL'] = "".XOOPS_URL."/modules/$mydirname/index.php?pa=viewads&lid=".addslashes($lid)."";
	$tags['THINKS_INTERESTING'] = "".constant($main_lang."_MESSAGE")."";
	$tags['YOU_CAN_VIEW_BELOW'] = "".constant($main_lang."_YOU_CAN_VIEW_BELOW")."";
	$tags['WEBMASTER'] = constant($main_lang."_WEBMASTER");
	$tags['NO_REPLY'] = constant($main_lang."_NOREPLY");
	$subject = "".constant($main_lang."_SUBJET")." ".$xoopsConfig['sitename']."";
	$xoopsMailer =& xoops_getMailer();
	$xoopsMailer->multimailer->isHTML(true);
	$xoopsMailer->useMail();


	if ( is_dir("language/".$xoopsConfig['language']."/mail_template/") ) {
	$xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH."/modules/$mydirname/language/".$xoopsConfig['language']."/mail_template/");
	} else {
	$xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH."/modules/$mydirname/language/english/mail_template/");
	}


	$xoopsMailer->setTemplate("listing_send_friend.tpl");
	$xoopsMailer->setFromEmail($ymail);
	$xoopsMailer->setToEmails($fmail);
	$xoopsMailer->setSubject($subject);

	$xoopsMailer->multimailer->AltBody = "text version of email";
 	$xoopsMailer->assign($tags);

	$xoopsMailer->send();
	echo $xoopsMailer->getErrors();

	redirect_header("index.php",3,constant($main_lang."_ALUMSEND"));
	exit();
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

    case "SendFriend":
	include(XOOPS_ROOT_PATH."/header.php");
	SendFriend($lid);
	include(XOOPS_ROOT_PATH."/footer.php");
	break;
	
    case "MailAlum":
	MailAlum($lid, $yname, $ymail, $fname, $fmail);
	break;

    default:
	redirect_header("index.php",1,""._RETURNGLO."");
	break;

}

?>