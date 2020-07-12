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
foreach ($_POST as $k => $v) {
	${$k} = $v;
}

if ($submit) {
	include("header.php");
	
	$mydirname = basename( dirname( __FILE__ ) ) ;

	require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;
	
	global $xoopsConfig, $xoopsDB, $myts, $meta, $mydirname;

	$result = $xoopsDB->query("select email, submitter, name, mname, lname FROM  ".$xoopsDB->prefix("alumni_listing")." WHERE lid = ".mysql_real_escape_string($id)."");

	while(list($email, $submitter, $name, $mname, $lname) = $xoopsDB->fetchRow($result)) {
		$name = $myts->htmlSpecialChars($name);
		$mname = $myts->htmlSpecialChars($mname);
		$lname = $myts->htmlSpecialChars($lname);
		$submitter = $myts->htmlSpecialChars($submitter);	
		if ($tele) {
			$teles = ""._ALUMNI_ORAT." $tele";
		}  else {
			$teles = "";
		}

		$message .= "$name $mname $lname\n";
		$message .= ""._ALUMNI_REMINDANN." "._ALUMNI_FROMANNOF." $sitename\n";
		$message .= ""._ALUMNI_MESSFROM." $namep    ".$meta['title']."\n\n";
		$message .= "\n";
		$message .= stripslashes("$messtext\n\n");
		$message .= "   "._ALUMNI_ENDMESS."\n\n";
		$message .= ""._ALUMNI_CANJOINT." $namep "._ALUMNI_TO." $post\n\n";
		$message .= "End of message \n\n";

		$subject = ""._ALUMNI_CONTACTAFTERANN."";
		$mail =& xoops_getMailer();
		$mail->useMail();
		$mail->setFromEmail($post);
		$mail->setToEmails($email);
		$mail->setSubject($subject);
		$mail->setBody($message);
		$mail->send();
		echo $mail->getErrors();
		
		$message .= "\n".$_SERVER['REMOTE_ADDR']."\n";
		$adsubject = $xoopsConfig['sitename']." "._ALUMNI_REPLY." ";
		$xoopsMailer =& xoops_getMailer();
		$xoopsMailer->useMail();
		$xoopsMailer->setToEmails($xoopsConfig['adminmail']);
		$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
		$xoopsMailer->setFromName($xoopsConfig['sitename']);
		$xoopsMailer->setSubject($adsubject);
		$xoopsMailer->setBody($message);
		$xoopsMailer->send();
		
	}
	redirect_header("index.php",1,_ALUMNI_MESSEND);
	exit();

} else {

	$lid = isset( $_GET['lid'] ) ? $_GET['lid'] : '' ;

	include("header.php");
	
	$mydirname = basename( dirname( __FILE__ ) ) ;
	require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;
	
	include(XOOPS_ROOT_PATH."/header.php");
	echo '<table width="100%" cellspacing="0" class="outer"><tr><td class="even">';
	echo "<script type=\"text/javascript\">
          function verify() {
                var msg = \""._ALUMNI_VALIDERORMSG."\\n__________________________________________________\\n\\n\";
                var errors = \"FALSE\";

				if (document.cont.namep.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDSUBMITTER."\\n\";
                }
				
				if (document.cont.post.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDEMAIL."\\n\";
                }
				
				if (document.cont.messtext.value == \"\") {
                        errors = \"TRUE\";
                        msg += \""._ALUMNI_VALIDMESS."\\n\";
                }
				
  
                if (errors == \"TRUE\") {
                        msg += \"__________________________________________________\\n\\n"._ALUMNI_VALIDMSG."\\n\";
                        alert(msg);
                        return false;
                }
          }
          </script>";

	echo "<b>"._ALUMNI_CONTACTAUTOR."</b><br /><br />";
	echo ""._ALUMNI_TEXTAUTO."<br />";
	echo "<form onsubmit=\"return verify();\" method=\"post\" action=\"contact.php\" name=\"cont\">";
	echo "<input type=\"hidden\" name=\"id\" value=\"$lid\" />";
	echo "<input type=\"hidden\" name=\"submit\" value=\"1\" />";

    if($xoopsUser) {
		$idd =$xoopsUser->getVar("name", "E");
		$idde =$xoopsUser->getVar("email", "E");
			}

	echo "<table width='100%' class='outer' cellspacing='1'>
    <tr>
      <td class='head'>"._ALUMNI_YOURNAME."</td>
      <td class='even'><input type=\"text\" name=\"namep\" size=\"40\" value=\"$idd\" /></td>
    </tr>
    <tr>
      <td class='head'>"._ALUMNI_YOUREMAIL."</td>
      <td class='even'><input type=\"text\" name=\"post\" size=\"40\" value=\"$idde\" /></td>
    </tr>
    <tr>
      <td class='head'>"._ALUMNI_YOURMESSAGE."</td>
      <td class='even'><textarea rows=\"5\" name=\"messtext\" cols=\"40\"></textarea></td>
    </tr>
	</table>
	<table class='outer'><tr><td>"._ALUMNI_YOUR_IP."&nbsp;
        <img src=\"".XOOPS_URL."/modules/$mydirname/ip_image.php\" alt=\"\" /><br />"._ALUMNI_IP_LOGGED."
        </td></tr></table>
	<br />
      <p><input type=\"submit\" value=\""._ALUMNI_SENDFR."\" /></p>
	</form>";
	echo '</td></tr></table>';
	include(XOOPS_ROOT_PATH."/footer.php");
}

?>