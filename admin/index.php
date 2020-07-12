<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.0x                             //
//                  By John Mordo from the myAds 2.04 Module                 //
//                    All Original credits left below this                   //
//                                                                           //
//                                                                           //
//                                                                           //
// 
// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller
// Author Website : pascal.e-xoops@perso-search.com
// Licence Type   : GPL
// ------------------------------------------------------------------------- //
	include("admin_header.php");
	include_once '../../../include/cp_header.php';
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;
	include_once (XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");

$myts =& MyTextSanitizer::getInstance();

#  function Index
#####################################################
function Index()
{
    global $hlpfile, $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $mydirname;

	include_once(XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
	$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");

	xoops_cp_header();
	include( './mymenu.php' ) ;
	
	// setting checker
	$grad_photo_dir = XOOPS_ROOT_PATH . "/modules/$mydirname/grad_photo/" ;
	if( ! is_writable( $grad_photo_dir ) || ! is_readable( $grad_photo_dir ) ) {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_ALUM_CHECKER."</legend><br />"; 
		echo "<font color='#FF0000'><b>"._AM_ALUMNI_DIRPERMS."".$grad_photo_dir."</b></font><br /><br />\n" ;
		echo "</fieldset><br />"; 
	}
	// setting checker
	$now_photo_dir = XOOPS_ROOT_PATH . "/modules/$mydirname/now_photo/" ;
	if( ! is_writable( $now_photo_dir ) || ! is_readable( $now_photo_dir ) ) {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_ALUMNI_CHECKER."</legend><br />"; 
		echo "<font color='#FF0000'><b>"._AM_ALUMNI_DIRPERMS."".$now_photo_dir."</b></font><br /><br />\n" ;
		echo "</fieldset><br />"; 
	}
	
	
	$result = $xoopsDB->query("select lid, name, mname, lname, year, studies, date from ".$xoopsDB->prefix("alumni_listing")." WHERE valid='No' order by lid");
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows>0) {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_ALUMNI_WAIT."</legend>"; 
		
		echo ""._AM_ALUMNI_THEREIS." <b>$numrows</b> "._AM_ALUMNI_WAIT."<br /><br />";
	
		echo "<table width=100% cellpadding=2 cellspacing=0 border=0>";
		$rank = 1;

		while(list($lid, $name, $mname, $lname, $year, $studies, $date) = $xoopsDB->fetchRow($result)) {
			$name = $myts->htmlSpecialChars($name);
			$mname = $myts->htmlSpecialChars($mname);
			$lname = $myts->htmlSpecialChars($lname);
			$year = $myts->htmlSpecialChars($year);
			$studies = $myts->htmlSpecialChars($studies);
			$date2 = formatTimestamp($date,"s");

			if(is_integer($rank/2)) {
				$color="bg3";
			} else {
				$color="bg4";
			}
			echo "<tr class='$color'><td><a href=\"index.php?op=IndexView&amp;lid=$lid\">$name $mname $lname</a></td><td align=right> $date2</td></tr>";
			$rank++;
		}
		echo "</table>";
		echo "</fieldset><br />"; 
		echo "<br />";
    } else {
		echo "<fieldset><legend style='font-weight: bold; color: #900;'>". _AM_ALUMNI_WAIT . "</legend>"; 
		echo "<br /> "._AM_ALUMNI_NOANNVAL."<br /><br />";
		echo "</fieldset><br />"; 
		echo "<br />";
    }

	// Modify Listing
	echo "<fieldset><legend style='font-weight: bold; color: #900;'>". _AM_ALUMNI_MODANN . "</legend>"; 
	list($numrows) = $xoopsDB->fetchRow($xoopsDB->query("select COUNT(*) FROM ".$xoopsDB->prefix("alumni_listing").""));
    if ($numrows>0) {
	   
	    echo "<form method=\"post\" action=\"index.php\">"
			."<b>"._AM_ALUMNI_MODANN."</b><br /><br />"
			.""._AM_ALUMNI_NUMALUM." <input type=\"text\" name=\"lid\" size=\"12\" maxlength=\"11\">&nbsp;&nbsp;"
			."<input type=\"hidden\" name=\"op\" value=\"Modify\">"
			."<input type=\"submit\" value=\""._AM_ALUMNI_MODIF."\">"
			."<br /><br />"._AM_ALUMNI_ALLMODANN.""
			."</form><center><a href=\"../index.php\">"._AM_ALUMNI_ACCESMYANN."</a></center>";
	    echo "</fieldset><br />"; 
		echo "<br />";
    }
	xoops_cp_footer();
}

	
	
#  function IndexView
#####################################################
function IndexView($lid)
{
//  global $xoopsDB, $xoopsConfig, $xoopsModule, $myts;
    global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig, $myts, $mydirname;
	
	include_once(XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
	$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");
	
	xoops_cp_header();
	
    $result = $xoopsDB->query("select lid, cid, name, mname, lname, year, studies, activities, extrainfo, occ, date, email, submitter, town, photo, photo2 from ".$xoopsDB->prefix("alumni_listing")." WHERE valid='No' AND lid='$lid'");
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows>0) {
		OpenTable();
		echo "<b>"._AM_ALUMNI_WAIT."</b><br /><br />";

		list($lid, $cid, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $photo, $photo2) = $xoopsDB->fetchRow($result);

		$date2 = formatTimestamp($date,"s");
	
		$name = $myts->htmlSpecialChars($name);
		$mname = $myts->htmlSpecialChars($mname);
		$lname = $myts->htmlSpecialChars($lname);
		$year = $myts->htmlSpecialChars($year);
		$studies = $myts->htmlSpecialChars($studies);
		$activities = $myts->displayTarea($activities,1,0,1,1,1);
		$extrainfo = $myts->displayTarea($extrainfo,1,0,1,1,1);
		$occ = $myts->htmlSpecialChars($occ);
		$email = $myts->htmlSpecialChars($email);
		$submitter = $myts->htmlSpecialChars($submitter);	
		$town = $myts->htmlSpecialChars($town);
		
	
	    echo "<form action=\"index.php\" method=\"post\">
			<table><tr>
			<td>"._AM_ALUMNI_NUMALUM." </td><td>$lid / $date2</td>
			</tr><tr>
			<td>"._AM_ALUMNI_SENDBY." </td><td>$submitter</td>
			</tr><tr>
			<td>"._AM_ALUMNI_NAME." </td><td><input type=\"text\" name=\"name\" size=\"30\" value=\"$name\"></td>
			</tr><tr>
			<td>"._AM_ALUMNI_MNAME." </td><td><input type=\"text\" name=\"mname\" size=\"30\" value=\"$mname\"></td>
			</tr><tr>
			<td>"._AM_ALUMNI_LNAME." </td><td><input type=\"text\" name=\"lname\" size=\"30\" value=\"$lname\"></td>
			</tr><tr>
			<td>"._AM_ALUMNI_EMAIL." </td><td><input type=\"text\" name=\"email\" size=\"30\" value=\"$email\"></td>";
			echo "</tr><tr><td>"._AM_ALUMNI_CAT."</td><td>";
	    $mytree->makeMySelBox("title", "title", $cid);
			echo "</td></tr><tr><td>"._AM_ALUMNI_CLASSOF." </td><td><input type=\"text\" name=\"year\" size=\"4\" value=\"$year\"></td>";
			echo "</tr><tr>
			<td>"._AM_ALUMNI_PHOTO2." </td><td><input type=\"text\" name=\"photo\" size=\"30\" value=\"$photo\"></td>
			</tr><tr>
			<td>"._AM_ALUMNI_PHOTO4." </td><td><input type=\"text\" name=\"photo2\" size=\"30\" value=\"$photo2\"></td>
			</tr>";
			echo "<tr>
			<td>"._AM_ALUMNI_REQUIRE." </td><td><input type=\"text\" name=\"studies\" size=\"30\" value=\"$studies\"</td>
			</tr>";

		$activities = "";
		echo "<tr><td class=\"outer\"><b>"._AM_ALUMNI_ACTIVITIES."</b></b></td><td class=\"odd\">";
		$wysiwyg_text_area= alumni_getEditor(_AM_ALUMNI_ACTIVITIES, 'activities', $activities, '100%', '200px','small');
		echo $wysiwyg_text_area->render();
		$extra_info ="";
		echo "<tr><td class=\"outer\"><b>"._AM_ALUMNI_EXTRAINFO."</b></b></td><td class=\"odd\">";
		$wysiwyg_extra_info_text= alumni_getEditor(_AM_ALUMNI_EXTRAINFO, 'extrainfo', $extrainfo, '100%', '200px','small');
		echo $wysiwyg_extra_info_text->render();

			echo "</td><tr>
			<td>"._AM_ALUMNI_OCC." </td><td><input type=\"text\" name=\"occ\" size=\"30\" value=\"$occ\"></td>
			</tr><tr>
			<td>"._AM_ALUMNI_TOWN." </td><td><input type=\"text\" name=\"town\" size=\"30\" value=\"$town\"></td>";
			echo "</tr><tr><td>&nbsp;</td><td><select name=\"op\">
			<option value=\"AlumniValid\"> "._AM_ALUMNI_OK."
			<option value=\"AlumniDel\"> "._AM_ALUMNI_DEL."
			</select><input type=\"submit\" value=\""._AM_ALUMNI_GO."\"></td>
			</tr></table>";
		echo "<input type=\"hidden\" name=\"valid\" value=\"Yes\">";
	    echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";
	    echo "<input type=\"hidden\" name=\"date\" value=\"$date\">";
	    echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">
			</form>";

	CloseTable();
	echo "<br />";
    } 
	
	xoops_cp_footer();
}

#  function Modify
#####################################################
function Modify($lid)
{
	// for XOOPS CODE by Tom
    //global $xoopsDB, $xoopsModule, $xoopsConfig, $myts;
    global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig, $myts, $mydirname;

	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
	include_once(XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
	$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");

	xoops_cp_header();
	include( './mymenu.php' ) ;
	echo "<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_ALUMNI_MODANN."</legend>";
    $result = $xoopsDB->query("select lid, cid, name, mname, lname, year, studies, activities, extrainfo, occ, date, email, submitter, town, valid, photo, photo2 from ".$xoopsDB->prefix("alumni_listing")." where lid=$lid");

    echo "<b>"._AM_ALUMNI_MODANN."</b><br /><br />";
	
    while(list($lid, $cid, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $valid, $photo, $photo2) = $xoopsDB->fetchRow($result)) {
	
		$name = $myts->htmlSpecialChars($name);
		$mname = $myts->htmlSpecialChars($mname);
		$lname = $myts->htmlSpecialChars($lname);
		$year = $myts->htmlSpecialChars($year);
		$studies = $myts->htmlSpecialChars($studies);
		$activities = $myts->displayTarea($activities,1,0,1,1,1);
		$extrainfo = $myts->displayTarea($extrainfo,1,0,1,1,1);
		$occ = $myts->htmlSpecialChars($occ);
		$submitter = $myts->htmlSpecialChars($submitter);	
		$town = $myts->htmlSpecialChars($town);
		$date2 = formatTimestamp($date,"s");
		
    	echo "<form action=\"index.php\" method=post>
		    <table class=\"outer\" border=0><tr>
			<td class=\"outer\">"._AM_ALUMNI_NUMALUM." </td><td class=\"odd\">$lid &nbsp; submitted on &nbsp; $date2</td>
			</tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_SENDBY." </td><td class=\"odd\">$submitter</td>
			</tr><tr>
			
			<td class=\"outer\">"._AM_ALUMNI_NAME." </td><td class=\"odd\"><input type=\"text\" name=\"name\" size=\"30\" value=\"$name\"></td>
			</tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_MNAME." </td><td class=\"odd\"><input type=\"text\" name=\"mname\"
			size=\"30\" value=\"$mname\"></td>
			</tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_LNAME." </td><td class=\"odd\"><input type=\"text\" name=\"lname\" size=\"30\" value=\"$lname\"></td>
			</tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_SCHOOL2." </td><td class=\"odd\">";
			$mytree->makeMySelBox("title", "title", $cid);
		echo "</td><tr>
		<td class=\"outer\">"._AM_ALUMNI_CLASSOF." </td><td class=\"odd\">
		<input type=\"text\" name=\"year\" size=\"4\" value=\"$year\"></td>
		</tr><tr>
		<td class=\"outer\">"._AM_ALUMNI_STUDIES." </td><td class=\"odd\">";
		echo "<input type=\"text\" name=\"studies\" size=\"30\" value=\"$studies\"></td>";
		echo "</tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_PHOTO2." </td><td class=\"odd\"><input type=\"text\" name=\"photo\" size=\"30\" value=\"$photo\"></td>
			</tr><tr>";
		echo "</td></tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_PHOTO4." </td><td class=\"odd\"><input type=\"text\" name=\"photo2\" size=\"30\" value=\"$photo2\"></td>
			</tr><tr>";

		$activities = "";
		echo "<tr><td class=\"outer\"><b>"._AM_ALUMNI_ACTIVITIES."</b></b></td><td class=\"odd\">";
		$wysiwyg_text_area= alumni_getEditor(_AM_ALUMNI_ACTIVITIES, 'activities', $activities, '100%', '200px','small');
		echo $wysiwyg_text_area->render();
		$extra_info ="";
		echo "<tr><td class=\"outer\"><b>"._AM_ALUMNI_EXTRAINFO."</b></b></td><td class=\"odd\">";
		$wysiwyg_extra_info_text= alumni_getEditor(_AM_ALUMNI_EXTRAINFO, 'extra_info', $extra_info, '100%', '200px','small');
		echo $wysiwyg_extra_info_text->render();

		echo "</td></tr>
		<tr>
		<td class=\"outer\">"._AM_ALUMNI_EMAIL." </td><td class=\"odd\"><input type=\"text\" name=\"email\" size=\"30\" value=\"$email\"></td>
			</tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_OCC." </td><td class=\"odd\"><input type=\"text\" name=\"occ\" size=\"30\" value=\"$occ\"></td>
			</tr><tr>
			<td class=\"outer\">"._AM_ALUMNI_TOWN." </td><td class=\"odd\"><input type=\"text\" name=\"town\" size=\"30\" value=\"$town\"></td>";
					
		$time = time();
		
		echo "</tr><tr>
			<td>&nbsp;</td><td><select name=\"op\">
			<option value=\"ModifyS\"> "._AM_ALUMNI_MODIF."
			<option value=\"AlumniDel\"> "._AM_ALUMNI_DEL."
			</select><input type=\"submit\" value=\""._AM_ALUMNI_GO."\"></td>
			</tr></table>";
		echo "<input type=\"hidden\" name=\"valid\" value=\"Yes\">";
	    echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";
	    echo "<input type=\"hidden\" name=\"date\" value=\"$time\">";
	    echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">
		</form><br />";
    	echo "</fieldset><br />"; 
		xoops_cp_footer();
	}
}

		 
#  function ModifyS
#####################################################
function ModifyS($lid, $cat, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $valid, $photo, $photo2)
{
    global $xoopsDB, $xoopsConfig, $myts;
	
	$name = $myts->htmlSpecialChars($name);
	$mname = $myts->htmlSpecialChars($mname);
	$lname = $myts->htmlSpecialChars($lname);
	$year = $myts->htmlSpecialChars($year);
	$studies = $myts->htmlSpecialChars($studies);
	$activities = $myts->displayTarea($activities,1,0,1,1,1);
	$extrainfo = $myts->displayTarea($extrainfo,1,0,1,1,1);
	$occ = $myts->htmlSpecialChars($occ);
	$submitter = $myts->htmlSpecialChars($submitter);
	$town = $myts->htmlSpecialChars($town);
	
	
    $xoopsDB->query("update ".$xoopsDB->prefix("alumni_listing")." set cid='$cat', name='$name', mname='$mname', lname='$lname', year='$year', studies='$studies', activities='$activities', extrainfo='$extrainfo', occ='$occ', date='$date', email='$email', submitter='$submitter', town='$town', valid='$valid', photo='$photo', photo2='$photo2' where lid=$lid");

	redirect_header("index.php",1,_AM_ALUMNI_JOBMOD);
	exit();
}


#  function AlumniDel
#####################################################
function AlumniDel($lid, $photo, $photo2)
{
    global $xoopsDB, $mydirname;
	
    $xoopsDB->query("delete from ".$xoopsDB->prefix("alumni_listing")." where lid=$lid");
	
	$destination = XOOPS_ROOT_PATH."/modules/$mydirname/grad_photo";
		if ($photo) {
			if (file_exists("$destination/$photo")) {
				unlink("$destination/$photo");
			}
		}
		
		$destination2 = XOOPS_ROOT_PATH."/modules/$mydirname/now_photo";
		if ($photo2) {
			if (file_exists("$destination2/$photo2")) {
				unlink("$destination2/$photo2");
			}
		}
	
	redirect_header("index.php",1,_AM_ALUMNI_JOBDEL);
	exit();
}


#  function AlumniValid
#####################################################
function AlumniValid($lid, $cat, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $valid, $photo, $photo2)
{
    global $xoopsDB, $xoopsConfig, $myts, $meta, $mydirname;

	$name = $myts->htmlSpecialChars($name);
	$mname = $myts->htmlSpecialChars($mname);
	$lname = $myts->htmlSpecialChars($lname);
	$year = $myts->htmlSpecialChars($year);
	$studies = $myts->htmlSpecialChars($studies);
	$activities = $myts->displayTarea($activities,1,0,1,1,1);
	$extrainfo = $myts->displayTarea($extrainfo,1,0,1,1,1);
	$occ = $myts->htmlSpecialChars($occ);
	$submitter = $myts->htmlSpecialChars($submitter);
	$town = $myts->htmlSpecialChars($town);
	
	
    $xoopsDB->query("update ".$xoopsDB->prefix("alumni_listing")." set cid='$cat', name='$name', mname='$mname', lname='$lname', year='$year', studies='$studies', activities='$activities', extrainfo='$extrainfo', occ='$occ', date='$date', email='$email', submitter='$submitter', town='$town', valid='$valid', photo='$photo', photo2='$photo2'  where lid=$lid");

	if ($email=="") {
	} else {	
		$message = ""._AM_ALUMNI_HELLO." $submitter \n\n "._AM_ALUMNI_ACCEPT."\n\n$name $mname $lname\n\n"._AM_ALUMNI_CLASSOF." $year\n"._AM_ALUMNI_STUDIES."$studies\n"._AM_ALUMNI_ACTIVITIES2."$activities\n"._AM_ALUMNI_OCC."$occ\n"._AM_ALUMNI_TOWN."\n $town\n\n"._AM_ALUMNI_CONSULTTO."\n ".XOOPS_URL."/modules/$mydirname/index.php?pa=viewlistings&amp;lid=$lid\n\n "._AM_ALUMNI_THANK."\n\n"._AM_ALUMNI_SEENON." ".$meta['title']."\n".XOOPS_URL."";
		$subject = ""._AM_ALUMNI_ACCEPT."";
		$mail =& xoops_getMailer();
		$mail->useMail();
		$mail->setFromName($meta['title']);
		$mail->setFromEmail($xoopsConfig['adminmail']);
		$mail->setToEmails($email);
		$mail->setSubject($subject);
		$mail->setBody($message);
		$mail->send();
		echo $mail->getErrors();
	}

	redirect_header("index.php",1,_AM_ALUMNI_JOBVALID);
	exit();
}



#####################################################
foreach ($_POST as $k => $v) {
	${$k} = $v;
}

$pa = isset( $_GET['pa'] ) ? $_GET['pa'] : '' ;

if(!isset($_POST['lid']) && isset($_GET['lid']) ) {
	$lid = $_GET['lid'] ;
}
if(!isset($_POST['op']) && isset($_GET['op']) ) {
	$op = $_GET['op'] ;
}
if (!isset($op)) {
	$op = '';
}

switch ($op) {

    case "IndexView":
    IndexView($lid);
    break;

    case "AlumniDel":
    AlumniDel($lid, $photo, $photo2);
    break;

    case "AlumniValid":
    AlumniValid($lid, $cid, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $valid, $photo, $photo2);
    break;

    case "Modify":
    Modify($lid);
    break;

    case "ModifyS":
    ModifyS($lid, $cid, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $valid, $photo, $photo2);
    break;

    default:
    Index();
    break;
}

?>