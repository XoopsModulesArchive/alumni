<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.3.x                          //
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
$main_lang =  '_' . strtoupper( $mydirname ) ;
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

function addindex($cid)
{
    global $xoopsDB, $xoopsConfig, $xoopsUser, $xoopsTheme, $xoopsLogger, $xoopsModule, $xoopsModuleConfig, $mydirname, $main_lang;

	include_once (XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");
	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
	include_once (XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
	$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");

	if ( !empty($_POST['cid']) ) {
		redirect_header("index.php",1,_ALUMNI_ADDLISTING);
		exit();
	}
		$photomax=$xoopsModuleConfig["".$mydirname."_photomax"];
		$photomax1=$xoopsModuleConfig["".$mydirname."_photomax"]/1024;

		echo "<script type=\"text/javascript\">
		  function verify() {
				var msg = \""._ALUMNI_VALIDERORMSG."\\n__________________________________________________\\n\\n\";
				var errors = \"FALSE\";
		
                if (document.add.cid.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDCAT."\\n\";
                }
				
                if (document.add.name.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDNAME."\\n\";
                }


				if (document.add.lname.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDLNAME."\\n\";
                }
	
				if (document.add.submitter.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDSUBMITTER."\\n\";
                }
				
				if (document.add.email.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDEMAIL."\\n\";
                }
				
				if (document.add.year.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDYEAR."\\n\";
                }
				
                if (errors == \"TRUE\") {
                        msg += \"__________________________________________________\\n\\n"._ALUMNI_VALIDMSG."\\n\";
                        alert(msg);
                        return false;
                }
          }
          </script>";

		list($numrows) = $xoopsDB->fetchRow($xoopsDB->query("select cid, title from ".$xoopsDB->prefix("alumni_categories").""));

		if ($numrows>0) {
			echo '<table width="100%" cellspacing="0" class="outer"><tr><td class="even">';
		if ( $xoopsModuleConfig["".$mydirname."_moderated"] == 1) {
			    echo "<b>"._ALUMNI_ADDLISTING3."</b><br /><br /><center>"._ALUMNI_JOBMODERATE."</center><br /><br />";
			} else {
				echo "<b>"._ALUMNI_ADDLISTING3."</b><br /><br /><center>"._ALUMNI_JOBNOMODERATE."</center><br /><br />";
			}

			echo "<form method=\"post\" action=\"addlisting.php\" enctype=\"multipart/form-data\" name=\"add\" onsubmit=\"return verify();\">";
			echo "<table width='100%' class='outer' cellspacing='1'><tr>";
			echo "<td class='outer'>"._ALUMNI_SCHOOL3." </td><td class='odd'>";
			
			$result = $xoopsDB->query("select cid, pid, title from ".$xoopsDB->prefix("alumni_categories")." where  cid=".$cid."");
			list($cid, $pid, $title) = $xoopsDB->fetchRow($result);
			
				echo "$title";

			echo "<input type=\"hidden\" name=\"cid\" value=\"$cid\" />
			</td>
				</tr><tr>
				<td class='outer'>"._ALUMNI_NAME." </td><td class='odd'><input type=\"text\" name=\"name\" size=\"30\" maxlength=\"100\" /></td>
				</tr><tr>
				<td class='outer'>"._ALUMNI_MNAME." </td><td class='odd'><input type=\"text\" name=\"mname\" size=\"30\" maxlength=\"100\" /></td>
				</tr>
				<tr>
				<td class='outer'>"._ALUMNI_LNAME." </td><td class='odd'><input type=\"text\" name=\"lname\" size=\"30\" maxlength=\"100\" /></td>
				</tr><tr>
				<td class='outer'>"._ALUMNI_YEAR2." </td><td class='odd'><input type=\"text\" name=\"year\" size=\"4\" maxlength=\"4\" /></td>
			</tr><tr>
			<td class='outer'>"._ALUMNI_STUDIES." </td><td class='odd'><input type=\"text\" name=\"studies\" size=\"30\" maxlength=\"100\" /></td>
			</tr><tr>
			<td class='outer'>"._ALUMNI_PHOTO2."</td><td class='odd'><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\" /><input type=\"file\" name=\"photo\" /> (&lt;  ";
			printf ("%.2f KB",$photomax1); 
			echo ")</td></tr>";
			echo "<tr>";
			echo "<td class='outer'>"._ALUMNI_RPHOTO2."</td><td class='odd'><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\" /><input type=\"file\" name=\"photo2\" /> (&lt;  ";
			printf ("%.2f KB",$photomax1); 
			echo ")</td></tr>";

		$activities = "";
		echo "<tr><td class=\"outer\"><b>"._ALUMNI_ACTIVITIES."</b></b></td><td class=\"odd\">";
		$wysiwyg_text_area= alumni_getEditor(_ALUMNI_ACTIVITIES, 'activities', $activities, '100%', '200px','small');
		echo $wysiwyg_text_area->render();
		$extrainfo ="";
		echo "</td></tr><tr><td class=\"outer\"><b>"._ALUMNI_EXTRAINFO."</b></b></td><td class=\"odd\">";
		$wysiwyg_extra_info= alumni_getEditor(_ALUMNI_EXTRAINFO, 'extrainfo', $extrainfo, '100%', '200px','small');
		echo $wysiwyg_extra_info->render();
		echo "</td>";
	if($xoopsUser) {
		$iddd =$xoopsUser->getVar("uid", "E");
		$idd =$xoopsUser->getVar("name", "E");// Real name
		$idde =$xoopsUser->getVar("email", "E");
		// Add by Tom
		$iddn =$xoopsUser->getVar("uname", "E");// user name
			}
	
			$time = time();
			
			// CHGED name pattern by Tom
			if ($idd) {
				echo "</tr><tr>
					<td class='outer'>"._ALUMNI_SURNAME." </td><td class='odd'><input type=\"text\" name=\"submitter\" size=\"30\" value=\"$idd\" /></td>";
			}else{
				echo "</tr><tr>
					<td class='outer'>"._ALUMNI_SURNAME." </td><td class='odd'><input type=\"text\" name=\"submitter\" size=\"30\" value=\"$iddn\" /></td>";
			}
			echo "</tr><tr>
				<td class='outer'>"._ALUMNI_EMAIL." </td><td class='odd'><input type=\"text\" name=\"email\" size=\"30\" value=\"$idde\" /></td>
				</tr><tr>
				<td class='outer'>"._ALUMNI_OCC." </td><td class='odd'><input type=\"text\" name=\"occ\" size=\"30\" /></td>
				</tr><tr>
				<td class='outer'>"._ALUMNI_TOWN." </td><td class='odd'><input type=\"text\" name=\"town\" size=\"30\" /></td>
				</tr>";

	if ($xoopsModuleConfig["alumni_use_captcha"] == '1') {
		echo "<tr><td class='outer'>"._ALUMNI_CAPTCHA." </td><td class='even'>";
$alumni_captcha = "";
	$alumni_captcha = (new XoopsFormCaptcha(_ALUMNI_CAPTCHA, "xoopscaptcha", false));
	echo $alumni_captcha->render();
}


	echo "</td></tr></table><br />
		<input type=\"hidden\" name=\"usid\" value=\"$iddd\" />
		<input type=\"hidden\" name=\"op\" value=\"AddListingsOk\" />";
				
		if ( $xoopsModuleConfig["".$mydirname."_moderated"] == 1) {
		echo "<input type=\"hidden\" name=\"valid\" value=\"No\" />";
			} else {
		echo "<input type=\"hidden\" name=\"valid\" value=\"Yes\" />";
			}
		echo "<input type=\"hidden\" name=\"lid\" value=\"0\" />
		<input type=\"hidden\" name=\"school\" value=\"$title\" />
		<input type=\"hidden\" name=\"date\" value=\"$time\" />
		<input type=\"submit\" value=\""._ALUMNI_VALIDATE."\" />";
		echo "</form>";
		echo '</td></tr></table>';
			}
	   	}

	
function AddListingsOk($lid, $cid, $name, $mname, $lname, $school, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $usid, $town, $valid, $_FILES, $_POST)
{
	global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $destination, $destination2, $myts, $xoopsLogger, $mydirname, $main_lang;

	include(XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");

	xoops_load("captcha");
	$xoopsCaptcha = XoopsCaptcha::getInstance();
	if( !$xoopsCaptcha->verify() ) {
        redirect_header( XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/index.php", 2, $xoopsCaptcha->getMessage() );
	}
	
	$photomax=$xoopsModuleConfig["".$mydirname."_photomax"];
	$maxwide=$xoopsModuleConfig["".$mydirname."_maxwide"];
	$maxhigh=$xoopsModuleConfig["".$mydirname."_maxhigh"];
	
	$name = $myts->htmlSpecialChars($_POST["name"]);
	$mname = $myts->htmlSpecialChars($_POST["mname"]);
	$lname = $myts->htmlSpecialChars($_POST["lname"]);
	$school = $myts->htmlSpecialChars($_POST["school"]);
	$year = $myts->htmlSpecialChars($_POST["year"]);
	$studies = $myts->htmlSpecialChars($_POST["studies"]);
	$activities = $myts->displayTarea($_POST["activities"],1,0,1,1,1);
	$extrainfo = $myts->displayTarea($_POST["extrainfo"],1,0,1,1,1);
	$occ = $myts->htmlSpecialChars($_POST["occ"]);
	$submitter = $myts->htmlSpecialChars($_POST["submitter"]);
	$town = $myts->htmlSpecialChars($_POST["town"]);
	
	$filename = '';
	
	if ( !empty($_FILES['photo']['name']) ) {
		include_once XOOPS_ROOT_PATH."/class/uploader.php";
		$updir = 'grad_photo/';
		$allowed_mimetypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png');
		$uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax, $maxwide, $maxhigh);
		$uploader->setTargetFileName($date.'_'.$_FILES['photo']['name']);
		$uploader->fetchMedia('photo');
		if (!$uploader->upload()) {
				$errors = $uploader->getErrors();
				 redirect_header("addlisting.php?cid=$cid", 3, $errors);
				//return False;
				exit();
		} else {
			$filename = $uploader->getSavedFileName();
		}
	}
	
	$filename2 = '';
	
	if ( !empty($_FILES['photo2']['name']) ) {
		include_once XOOPS_ROOT_PATH."/class/uploader.php";
		$updir = 'now_photo/';
		$allowed_mimetypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png');
		$uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax, $maxwide, $maxhigh);
		$uploader->setTargetFileName($date.'_'.$_FILES['photo2']['name']);
		$uploader->fetchMedia('photo2');
		if (!$uploader->upload()) {
				$errors = $uploader->getErrors();
				 redirect_header("addlisting.php?cid=$cid", 3, $errors);
				return False;
				exit();
		} else {
			$filename2 = $uploader->getSavedFileName();
		}
	}

	$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("alumni_listing")." values ('', '$cid', '$name', '$mname', '$lname', '$school', '$year', '$studies', '$activities', '$extrainfo', '$occ', '$date', '$email', '$submitter', '$usid',  '$town',  '$valid', '$filename', '$filename2', '0')");
		

if($valid == 'Yes') {

	$notification_handler =& xoops_gethandler('notification');
	$lid = $xoopsDB->getInsertId();
	$tags=array();
	$tags['TITLE'] = "".$name."". $mname."". $lname;
	$tags['ADDED_TO_CAT'] = constant($main_lang."_ADDED_TO_CAT");
	$tags['RECIEVING_NOTIF'] = constant($main_lang."_RECIEVING_NOTIF");
	$tags['ERROR_NOTIF'] = constant($main_lang."_ERROR_NOTIF");
	$tags['WEBMASTER'] = constant($main_lang."_WEBMASTER");
	$tags['HELLO'] = constant($main_lang."_HELLO");
	$tags['FOLLOW_LINK'] = constant($main_lang."_FOLLOW_LINK");
	$tags['TYPE'] = $year;
	$tags['LINK_URL'] = XOOPS_URL . '/modules/'.$mydirname.'/index.php?pa=viewlistings'. '&lid=' . addslashes($lid);
	$sql = "SELECT title FROM " . $xoopsDB->prefix("".$mydirname."_categories") . " WHERE cid=" . addslashes($cid);
	$result2 = $xoopsDB->query($sql);
	$row = $xoopsDB->fetchArray($result2);
	$tags['CATEGORY_TITLE'] = $row['title'];
	$tags['CATEGORY_URL'] = XOOPS_URL . '/modules/'.$mydirname.'/index.php?pa=view&cid="' . addslashes($cid);
	$notification_handler =& xoops_gethandler('notification');
	$notification_handler->triggerEvent('global', 0, 'new_listing', $tags);
	$notification_handler->triggerEvent('category', $cid, 'new_listing', $tags);
	$notification_handler->triggerEvent ('listing', $lid, 'new_listing', $tags );

} else {

	$tags =array();
	$subject =  "".constant($main_lang."_NEW_WAITING_SUBJECT")."";
	$tags['TITLE'] = "".$name."". $mname."". $lname;
	$tags['DESCTEXT'] = $desctext;
	$tags['ADMIN'] = constant($main_lang."_ADMIN");
	$tags['NEW_WAITING'] = constant($main_lang."_NEW_WAITING");
	$tags['PLEASE_CHECK'] = constant($main_lang."_PLEASE_CHECK");
	$tags['WEBMASTER'] = constant($main_lang."_WEBMASTER");
	$tags['HELLO'] = constant($main_lang."_HELLO");
	$tags['FOLLOW_LINK'] = constant($main_lang."_FOLLOW_LINK");
	$tags['TYPE'] = $year;
	$tags['NEED_TO_LOGIN'] = constant($main_lang."_NEED_TO_LOGIN");
	$tags['ADMIN_LINK'] = XOOPS_URL . '/modules/'.$mydirname.'/admin/index.php';
	$sql = "SELECT title FROM " . $xoopsDB->prefix("".$mydirname."_categories") . " WHERE cid=" . addslashes($cid);
	$result2 = $xoopsDB->query($sql);
	$row = $xoopsDB->fetchArray($result2);
	$tags['CATEGORY_TITLE'] = $row['title'];
	$tags['NEWAD'] = constant($main_lang."_NEWAD");

		$mail =& xoops_getMailer();
		$mail->setTemplateDir(XOOPS_ROOT_PATH."/modules/".$mydirname."/language/".$xoopsConfig['language']."/mail_template/");
		$mail->setTemplate("listing_notify_admin.tpl");
		$mail->useMail();
		$mail->multimailer->isHTML(true);
		$mail->setFromName($xoopsConfig['sitename']);
		$mail->setFromEmail($xoopsConfig['adminmail']);
		$mail->setToEmails($xoopsConfig['adminmail']);
		$mail->setSubject($subject);
		$mail->assign($tags);
		$mail->send();
		echo $mail->getErrors();
		}

	redirect_header("index.php",12,_ALUMNI_JOBADDED);
	exit();
}


#######################################################
foreach ($_POST as $k => $v) {
	${$k} = $v;
}

if(!isset($_POST['cid']) && isset($_GET['cid']) ) {
	$cid = $_GET['cid'] ;
}

if(!isset($_POST['op']) && isset($_GET['op']) ) {
	$op = $_GET['op'] ;
}

if (!isset($op)) {
	$op = '';
}

switch($op) {	
	case "AddListingsOk":
   	AddListingsOk($lid, $cid, $name, $mname, $lname, $school, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $usid, $town, $valid, $_FILES, $_POST);
   	break;

	default:
	include(XOOPS_ROOT_PATH."/header.php");
	addindex($cid);
	include(XOOPS_ROOT_PATH."/footer.php");
	break;
}
	
?>