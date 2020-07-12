<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.0x                           //
//                  By John Mordo from the myAds 2.04 Module                 //
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

$myts =& MyTextSanitizer::getInstance();
$module_id = $xoopsModule->getVar('mid');
if (is_object($xoopsUser)) {
    $groups = $xoopsUser->getGroups();
} else {
	$groups = XOOPS_GROUP_ANONYMOUS;
}
$gperm_handler =& xoops_gethandler('groupperm');
if (isset($_POST['item_id'])) {
    $perm_itemid = intval($_POST['item_id']);
} else {
    $perm_itemid = 0;
}
//If no access
if (!$gperm_handler->checkRight("".$mydirname."_submit", $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL."/modules/$mydirname/index.php", 3, _NOPERM);
    exit();
}

function AlumniDel($lid='0', $ok='0')
{
	global $xoopsDB, $xoopsUser, $xoopsConfig, $xoopsTheme, $xoopsLogger, $mydirname;

	$result = $xoopsDB->query("select usid, photo, photo2 FROM ".$xoopsDB->prefix("".$mydirname."_listing")." where lid= ".mysql_real_escape_string($lid)."");
	list($usid, $photo) = $xoopsDB->fetchRow($result);

	if ($xoopsUser) {
		$calusern = $xoopsUser->getVar("uid", "E");
		if ($usid == $calusern) {
			if($ok==1) {
			    $xoopsDB->queryf("delete from ".$xoopsDB->prefix("".$mydirname."_listing")." where lid= ".mysql_real_escape_string($lid)."");
				if($photo){
					$destination = XOOPS_ROOT_PATH."/modules/$mydirname/grad_photo";
					if (file_exists("$destination/$photo")) {
						unlink("$destination/$photo");
					}
				}
				
				if($photo2){
					$destination2 = XOOPS_ROOT_PATH."/modules/$mydirname/now_photo";
					if (file_exists("$destination2/$photo2")) {
						unlink("$destination2/$photo2");
					}
				}
				
				redirect_header("index.php",1,_ALUMNI_JOBDEL);
				exit();

			} else {
				OpenTable();
				echo "<br /><center>";
				echo "<b>"._ALUMNI_SURDELANN."</b><br /><br />";
			}
			echo "[ <a href=\"modlisting.php?op=AlumniDel&amp;lid=$lid&amp;ok=1\">"._ALUMNI_OUI."</a> | <a href=\"index.php\">"._ALUMNI_NON."</a> ]<br /><br />";
			CloseTable();
		}
	}
}


function ModAlumni($lid='0')
{
	global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsUser, $xoopsModuleConfig, $xoopsTheme, $myts, $xoopsLogger, $mydirname;

	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

	echo "<script language=\"javascript\">\nfunction CLA(CLA) { var MainWindow = window.open (CLA, \"_blank\",\"width=500,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no\");}\n</script>";
	include(XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");
	include_once(XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
	$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");
	
	$photomax=$xoopsModuleConfig["".$mydirname."_photomax"];
	$photomax1=$xoopsModuleConfig["".$mydirname."_photomax"]/1024;

    $result = $xoopsDB->query("select lid, cid, name, mname, lname, year, studies, activities, extrainfo, occ, date, email, submitter, usid, town, valid, photo, photo2 from ".$xoopsDB->prefix("".$mydirname."_listing")." where lid=".mysql_real_escape_string($lid)."");
    list($lid, $cide, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $usid, $town, $valid, $photo_old, $photo2_old) = $xoopsDB->fetchRow($result);
	
	if ($xoopsUser) {
		$calusern = $xoopsUser->uid();
		if ($usid == $calusern) {

			echo "<fieldset><legend style='font-weight: bold; color: #900;'>"._ALUMNI_MODIFY2."</legend>";
	
			$name = $myts->htmlSpecialChars($name);
			$mname = $myts->htmlSpecialChars($mname);
			$lname = $myts->htmlSpecialChars($lname);
			$year = $myts->htmlSpecialChars($year);
			$studies = $myts->htmlSpecialChars($studies);
			$activities = $myts->displayTarea($activities,1,1,1);
			$extrainfo = $myts->displayTarea($extrainfo,1,1,1);
			$occ = $myts->htmlSpecialChars($occ);
			$submitter = $myts->htmlSpecialChars($submitter);
			$town = $myts->htmlSpecialChars($town);
	
			$useroffset = "";
		    if($xoopsUser) {
				$timezone = $xoopsUser->timezone();
				if(isset($timezone)){
					$useroffset = $xoopsUser->timezone();
				}else{
					$useroffset = $xoopsConfig['default_TZ'];
				}
			}
			$dates = ($useroffset*3600) + $date;	
			$dates = formatTimestamp($date,"s");
		
			echo "<form action=\"modlisting.php\" method=\"post\" enctype=\"multipart/form-data\">
			<table class=\"outer\"><tr>
			<td class=\"outer\"><b>"._ALUMNI_NUMANNN."</b></td><td class=\"odd\">$lid "._ALUMNI_DU." $dates</td>
			</tr><tr>
			<td class=\"outer\"><b>"._ALUMNI_SENDBY."</b></td><td class=\"odd\">$submitter</td>
			</tr><tr>
			<td class=\"outer\"><b>"._ALUMNI_NAME."</b></td><td class=\"odd\"><input type=\"text\" name=\"name\" size=\"30\" value=\"$name\"></td>
			</tr><tr>
			<td class=\"outer\"><b>"._ALUMNI_MNAME."</b></td><td class=\"odd\"><input type=\"text\" name=\"mname\" size=\"30\" value=\"$mname\"></td>
			</tr><tr>
			<td class=\"outer\"><b>"._ALUMNI_LNAME."</b></td><td class=\"odd\"><input type=\"text\" name=\"lname\" size=\"40\" value=\"$lname\"></td></tr>
			<td class=\"outer\"><b>"._ALUMNI_SCHOOL3."</b></td><td class=\"odd\">";
			$mytree->makeMySelBox("title", "title", $cide);
	    	echo "</td></tr><tr>";
			echo "<td class=\"outer\"><b>"._ALUMNI_YEAR2."</b></td><td class=\"odd\">
			<input type=\"text\" name=\"year\" size=\"4\" value=\"$year\"></td>";
			echo "</tr><tr>";
			echo "<td class=\"outer\"><b>"._ALUMNI_STUDIES."</b></td><td class=\"odd\">
			<input type=\"text\" name=\"studies\" size=\"40\" value=\"$studies\">";
			echo "</td></tr>";
//		$activities = "";
		echo "<tr><td class=\"outer\"><b>"._ALUMNI_ACTIVITIES."</b></b></td><td class=\"odd\">";
		$wysiwyg_text_area= alumni_getEditor( _ALUMNI_ACTIVITIES, 'activities', $activities, '100%', '200px','');
		echo $wysiwyg_text_area->render();
//		$extrainfo ="";
		echo "<tr><td class=\"outer\"><b>"._ALUMNI_EXTRAINFO."</b></b></td><td class=\"odd\">";
		$wysiwyg_extra_info_text= alumni_getEditor( _ALUMNI_EXTRAINFO, 'extrainfo', $extrainfo, '100%', '200px','');
		echo $wysiwyg_extra_info_text->render();
		echo "</td>";
		echo "</tr><tr><td class=\"outer\"><b>"._ALUMNI_EMAIL."</b></td><td class=\"odd\"><input type=\"text\" name=\"email\" size=\"30\" value=\"$email\"></td>
			</tr><tr>
			<td class=\"outer\"><b>"._ALUMNI_OCC." </b></td><td class=\"odd\"><input type=\"text\" name=\"occ\" size=\"30\" value=\"$occ\"></td>
			</tr><tr>
			<td class=\"outer\"><b>"._ALUMNI_TOWN." </b></td><td class=\"odd\"><input type=\"text\" name=\"town\" size=\"30\" value=\"$town\"></td>
			</tr><tr>";
			echo "</td><tr>";
			if ($photo_old) {
				echo "</tr><td class=\"outer\"><b>"._ALUMNI_ACTUALPICT."</b></td><td class=\"odd\"><a href=\"javascript:CLA('display-image.php?lid=$lid')\">$photo_old</a> <input type=\"hidden\" name=\"photo_old\" value=\"$photo_old\"><br /> <input type=\"checkbox\" name=\"supprim\" value=\"yes\"> "._ALUMNI_DELPICT."</td>
				</tr><tr>";
				echo "<td class=\"outer\"><b>"._ALUMNI_NEWPICT."</b></td><td class=\"odd\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\"><input type=\"file\" name=\"photo\"><br /> (<  ";
				printf ("%.2f KB",$photomax1);
				echo ")</td>";
			} else {
				echo "<td class=\"outer\"><b>"._ALUMNI_PHOTO."</b></td><td class=\"odd\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\"><input type=\"file\" name=\"photo\"><br /> (<  ";
				printf ("%.2f KB",$photomax1);
				echo ")</td>";
			}
			echo "<tr>";
			if ($photo2_old) {
				echo "</tr><td class=\"outer\"><b>"._ALUMNI_ACTUALPICT2."</b></td><td class=\"odd\"><a href=\"javascript:CLA('display-image2.php?lid=$lid')\">$photo2_old</a> <input type=\"hidden\" name=\"photo2_old\" value=\"$photo2_old\"><br /> <input type=\"checkbox\" name=\"supprim2\" value=\"yes\"> "._ALUMNI_DELPICT."</td>
				</tr><tr>";
				echo "<td class=\"outer\"><b>"._ALUMNI_NEWPICT2."</b></td><td class=\"odd\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\"><input type=\"file\" name=\"photo2\"><br /> (<  ";
				printf ("%.2f KB",$photomax1);
				echo ")</td>";
			} else {
				echo "<td class=\"outer\"><b>"._ALUMNI_RPHOTO."</b></td><td class=\"odd\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\"><input type=\"file\" name=\"photo2\"><br /> (<  ";
				printf ("%.2f KB",$photomax1);
				echo ")</td>";
			}
			echo "</tr><tr>";
			echo "<td colspan=2><input type=\"submit\" value=\""._ALUMNI_MODIFY."\"></td>
			</tr></table>";
			echo "<input type=\"hidden\" name=\"op\" value=\"ModAlumniS\">";
		
			if ($xoopsModuleConfig["".$mydirname."_moderated"] == '1') {
			    echo "<input type=\"hidden\" name=\"valid\" value=\"No\">";
				echo "<br />"._ALUMNI_MODIFBEFORE."<br />";
			} else {
				echo "<input type=\"hidden\" name=\"valid\" value=\"Yes\">";
			}
		
	    	echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";
	    	echo "<input type=\"hidden\" name=\"date\" value=\"$date\">";
	    	echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">		".$GLOBALS['xoopsGTicket']->getTicketHtml( __LINE__ , 1800 , 'token')."";
			echo "</form><br />";
    		echo "</fieldset><br />";
       		}
	}
}


function ModAlumniS($lid, $cat, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $valid, $photo, $photo_old, $photoS_size, $photoS_name, $photo2, $photo2_old, $photo2S_size, $photo2S_name, $_FILES, $supprim, $supprim2)
{
	global $xoopsDB, $xoopsConfig, $xoopsModuleConfig, $myts, $xoopsLogger, $mydirname, $xoopsGTicket;
	
	if ( ! $xoopsGTicket->check( true , 'token' ) ) {
		redirect_header(XOOPS_URL."/modules/$mydirname/index.php", 3,$xoopsGTicket->getErrors());
	}

	$destination = XOOPS_ROOT_PATH."/modules/$mydirname/grad_photo";
	
	if($supprim == "yes"){
		if (file_exists("$destination/$photo_old")) {
			 unlink("$destination/$photo_old");
		}
		
		$photo_old = "";
	}
	
	$destination2 = XOOPS_ROOT_PATH."/modules/$mydirname/now_photo";
	
	if($supprim2 == "yes"){
		if (file_exists("$destination2/$photo2_old")) {
			 unlink("$destination2/$photo2_old");
		}
		
		$photo2_old = "";
	}
	
	$name = $myts->htmlSpecialChars($name);
	$mname = $myts->htmlSpecialChars($mname);
	$lname = $myts->htmlSpecialChars($lname);
	$year = $myts->htmlSpecialChars($year);
	$studies = $myts->htmlSpecialChars($studies);
	$activities = $myts->displayTarea($activities,1,1,1);
	$extrainfo = $myts->displayTarea($extrainfo,1,1,1);
	$occ = $myts->htmlSpecialChars($occ);
	$submitter = $myts->htmlSpecialChars($submitter);
	$town = $myts->htmlSpecialChars($town);
	
	if ( !empty($_FILES['photo']['name']) ) {
		include_once XOOPS_ROOT_PATH."/class/uploader.php";
		$updir = 'grad_photo/';
		$allowed_mimetypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png');
		$uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax = $xoopsModuleConfig["".$mydirname."_photomax"], $xoopsModuleConfig["".$mydirname."_maxwide"], $maxhigh = $xoopsModuleConfig["".$mydirname."_maxhigh"]);
		$uploader->setTargetFileName($date.'_'.$_FILES['photo']['name']);
		$uploader->fetchMedia('photo');
		if (!$uploader->upload()) {
				$errors = $uploader->getErrors();
				 redirect_header("?op=ModAlumni&amp;lid=$lid", 3, $errors);
				exit();
		} else {
			if ($photo_old) {
				if (@file_exists("$destination/$photo_old")) {
					 unlink("$destination/$photo_old");
				}
			}
		$photo_old = $uploader->getSavedFileName();
		}		
	}
	
	if ( !empty($_FILES['photo2']['name']) ) {
		include_once XOOPS_ROOT_PATH."/class/uploader.php";
		$updir2 = 'now_photo/';
		$allowed_mimetypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png');
		$uploader = new XoopsMediaUploader($updir2, $allowed_mimetypes, $photomax = $xoopsModuleConfig["".$mydirname."_photomax"], $xoopsModuleConfig["".$mydirname."_maxwide"], $maxhigh = $xoopsModuleConfig["".$mydirname."_maxhigh"]);
		$uploader->setTargetFileName($date.'_'.$_FILES['photo2']['name']);
		$uploader->fetchMedia('photo2');
		if (!$uploader->upload()) {
				$errors = $uploader->getErrors();
				 redirect_header("?op=ModAlumni&amp;lid=$lid", 3, $errors);
				exit();
		} else {
			if ($photo2_old) {
				if (@file_exists("$destination2/$photo2_old")) {
					 unlink("$$destination2/$photo2_old");
				}
			}
		$photo2_old = $uploader->getSavedFileName();
		}		
	}
	
    $xoopsDB->query("update ".$xoopsDB->prefix("".$mydirname."_listing")." set cid='$cat', name='$name', mname='$mname', lname='$lname', year='$year', studies='$studies', activities='$activities', extrainfo='$extrainfo', occ='$occ', date='$date', email='$email', submitter='$submitter', town='$town', valid='$valid', photo='$photo_old', photo2='$photo2_old' where lid= ".mysql_real_escape_string($lid)."");

	redirect_header("index.php",1,_ALUMNI_JOBMOD2);
	exit();
}

####################################################
foreach ($_POST as $k => $v) {
	${$k} = $v;
}

$ok = isset( $_GET['ok'] ) ? $_GET['ok'] : '' ;

if(!isset($_POST['lid']) && isset($_GET['lid']) ) {
	$lid = $_GET['lid'] ;
}
if(!isset($_POST['op']) && isset($_GET['op']) ) {
	$op = $_GET['op'] ;
}

switch ($op) {

    case "ModAlumni":
	include(XOOPS_ROOT_PATH."/header.php");
    ModAlumni($lid);
	include(XOOPS_ROOT_PATH."/footer.php");
    break;
	
	case "ModAlumniS":
    ModAlumniS($lid, $cid, $name, $mname, $lname, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $town, $valid, $photo, $photo_old, $photo_size, $photo_name, $photo2, $photo2_old, $photo2_size, $photo2_name, $_FILES, $supprim, $supprim2);
    break;

    case "AlumniDel":
	include(XOOPS_ROOT_PATH."/header.php");
    AlumniDel($lid, $ok);
	include(XOOPS_ROOT_PATH."/footer.php");
    break;

    default:
	redirect_header("index.php",1,""._RETURNANN."");
	break;
}

?>