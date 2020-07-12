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
include_once (XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
include_once (XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");
	
#  function AlumniNewCat
#####################################################
function AlumniNewCat($cat)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $usecat, $classm, $myts, $mydirname;

	$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");

	xoops_cp_header();
    include( './mymenu.php' ) ;
	echo "<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_ALUMNI_ADDSUBCAT."</legend>"; 
	ShowImg();
	
	echo "<form method=\"post\" action=\"category.php\" name=\"imcat\"><input type=\"hidden\" name=\"op\" value=\"AlumniAddCat\"></font><br /><br />
		<table border=0>
    <tr>
      <td>"._AM_ALUMNI_CATNAME." </td><td colspan=2><input type=\"text\" name=\"title\" size=\"30\" maxlength=\"100\">&nbsp; "._AM_ALUMNI_IN." &nbsp;";

	$result = $xoopsDB->query("select pid, title, img, scphoto, ordre from ".$xoopsDB->prefix("alumni_categories")." where cid=$cat");
	list($pid, $title, $img, $scphoto, $ordre) = $xoopsDB->fetchRow($result);
	$mytree->makeMySelBox("title", "title", $cat, 1);
	
	echo "</td>
	</tr>
    <tr>
      <td>"._AM_ALUMNI_IMGCAT."  </td><td colspan=2><select name=\"img\" onChange=\"showimage()\">";
        
	$rep = XOOPS_ROOT_PATH."/modules/$mydirname/images/cat";
	$handle=opendir($rep);
	while ($file = readdir($handle)) {
		$filelist[] = $file;
	}
	asort($filelist);
	while (list ($key, $file) = each ($filelist)) {
		if (!ereg(".gif|.jpg|.png",$file)) {
			if ($file == "." || $file == "..") $a=1;
		} else {
			if ($file == "default.gif") {
				echo "<option value=$file selected>$file</option>";
			} else {
				echo "<option value=$file>$file</option>";
			}
		}
	}
	echo "</select>&nbsp;&nbsp;<img src=\"".XOOPS_URL."/modules/$mydirname/images/cat/default.gif\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td colspan=2>"._AM_ALUMNI_REPIMGCAT." /modules/$mydirname/images/cat/</td></tr><tr><td><b>"._AM_ALUMNI_IFSCHOOL."</b></td></tr></table>";
	
	
    
	echo "<hr />";
	echo "<table border=0><tr>
	<td>"._AM_ALUMNI_SCADDRESS."</td><td><input type=\"text\" name=\"scaddress\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCADDRESS2."</td><td><input type=\"text\" name=\"scaddress2\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCCITY."</td><td><input type=\"text\" name=\"sccity\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCSTATE."</td><td><input type=\"text\" name=\"scstate\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCZIP."</td><td><input type=\"text\" name=\"sczip\" size=\"30\" maxlength=\"20\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCPHONE."</td><td><input type=\"text\" name=\"scphone\" size=\"30\" maxlength=\"25\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCFAX."</td><td><input type=\"text\" name=\"scfax\" size=\"30\" maxlength=\"25\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCMOTTO."</td><td><input type=\"text\" name=\"scmotto\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCURL."</td><td><input type=\"text\" name=\"scurl\" size=\"30\" maxlength=\"50\"></td></tr>";
	if ($xoopsModuleConfig["".$mydirname."_classm"] = "ordre") {
		echo "<tr><td>"._AM_ALUMNI_ORDER." </td><td><input type=\"text\" name=\"ordre\" size=\"4\"></td>";
	
	ShowImg2();
	echo "<tr>
      <td>"._AM_ALUMNI_SCPHOTO."  </td><td colspan=2><select name=\"scphoto\" onchange=\"showimage2()\">";
	$none =  _AM_ALUMNI_NONE;   
	$rep2 = XOOPS_ROOT_PATH."/modules/$mydirname/images/schools";
	$handle2=opendir($rep2);
	while ($file2 = readdir($handle2)) {
		$filelist2[] = $file2;
	}
	asort($filelist2);
	while (list ($key, $file2) = each ($filelist2)) {
		if (!ereg(".gif|.jpg|.png",$file2)) {
			if ($file2 == "." || $file2 == "..") $a=1;
			echo "<option value=$file2 select>$none</option>";
			} else {
				echo "<option value=$file2>$file2</option>";
			}
		}
	echo "</select>&nbsp;&nbsp;<img src=\"".XOOPS_URL."/modules/$mydirname/images/schools/$scphoto\" name=\"scphoto\" align=\"middle\"> </td></tr><tr><td>&nbsp;</td><td colspan=2>"._AM_ALUMNI_REPIMGCAT." /modules/$mydirname/images/schools/</td></tr>";
	
	
	echo "<td><input type=\"submit\" value=\""._AM_ALUMNI_ADD."\"></td></tr>";
	} else {
		echo "<tr><td colspan=3><input type=\"submit\" value=\""._AM_ALUMNI_ADD."\"></td></tr>";
	
	}
	echo "</table>
	    </form>";
	echo "<br />";
	
	echo "</fieldset><br />";
	xoops_cp_footer();
}
	
	
#  function AlumniModCat
#####################################################
function AlumniModCat($cat)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $mydirname;

	include_once(XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
	$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");
	
	xoops_cp_header();
    include( './mymenu.php' ) ;
	echo "<fieldset><legend style='font-weight: bold; color: #900;'>". _AM_ALUMNI_MODIFCAT . "</legend>"; 
	ShowImg();
	ShowImg2();
	$result = $xoopsDB->query("select pid, title, scaddress,  scaddress2, sccity, scstate, sczip, scphone, scfax, scmotto, scurl, img, scphoto, ordre from ".$xoopsDB->prefix("alumni_categories")." where cid=$cat");
	list($pid, $title, $scaddress, $scaddress2, $sccity, $scstate, $sczip, $scphone, $scfax, $scmotto, $scurl, $imgs, $scphoto, $ordre) = $xoopsDB->fetchRow($result);

	$title = $myts->htmlSpecialChars($title);
	$scaddress = $myts->htmlSpecialChars($scaddress);
	$scaddress2 = $myts->htmlSpecialChars($scaddress2);
	$sccity = $myts->htmlSpecialChars($sccity);
	$scstate = $myts->htmlSpecialChars($scstate);
	$sczip = $myts->htmlSpecialChars($sczip);
	$scphone = $myts->htmlSpecialChars($scphone);
	$scfax = $myts->htmlSpecialChars($scfax);
	$scmotto = $myts->htmlSpecialChars($scmotto);
	
	echo "<form action=\"category.php\" method=\"post\" name=\"imcat\">
		<table border=\"0\"><tr>
	<td>"._AM_ALUMNI_CATNAME."   </td><td><input type=\"text\" name=\"title\" value=\"$title\" size=\"30\" maxlength=\"50\">&nbsp; "._AM_ALUMNI_IN." &nbsp;";
	$mytree->makeMySelBox("title", "title", $pid, 1);
	echo "</td></tr><tr>
	<td>"._AM_ALUMNI_IMGCAT."  </td><td><select name=\"img\" onChange=\"showimage()\">";	  
	  
	$rep = XOOPS_ROOT_PATH."/modules/$mydirname/images/cat";
	$handle=opendir($rep);
	while ($file = readdir($handle)) {
		$filelist[] = $file;
	}
	asort($filelist);
	while (list ($key, $file) = each ($filelist)) {
		if (!ereg(".gif|.jpg|.png",$file)) {
			if ($file == "." || $file == "..") $a=1;
		} else {
			if ($file == $imgs) {
				echo "<option value=$file selected>$file</option>";
			} else {
				echo "<option value=$file>$file</option>";
			}
		}
	}
	echo "</select>&nbsp;&nbsp;<img src=\"".XOOPS_URL."/modules/$mydirname/images/cat/$imgs\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td>"._AM_ALUMNI_REPIMGCAT." /modules/$mydirname/images/cat/</td></tr>
	<tr><td><b>"._AM_ALUMNI_IFSCHOOL."</b></td></tr></table>";
	
	echo "<hr />";
	echo "<table border=0><tr>
	<td>"._AM_ALUMNI_SCADDRESS."</td><td><input type=\"text\" name=\"scaddress\" value=\"$scaddress\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCADDRESS2."</td><td><input type=\"text\" name=\"scaddress2\" value=\"$scaddress2\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCCITY."</td><td><input type=\"text\" name=\"sccity\" value=\"$sccity\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCSTATE."</td><td><input type=\"text\" name=\"scstate\" value=\"$scstate\" size=\"30\" maxlength=\"50\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCZIP."</td><td><input type=\"text\" name=\"sczip\" value=\"$sczip\" size=\"15\" maxlength=\"20\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCPHONE."</td><td><input type=\"text\" name=\"scphone\" value=\"$scphone\" size=\"20\" maxlength=\"25\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCFAX."</td><td><input type=\"text\" name=\"scfax\" value=\"$scfax\" size=\"20\" maxlength=\"25\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCMOTTO."</td><td><input type=\"text\" name=\"scmotto\" value=\"$scmotto\" size=\"30\" maxlength=\"100\"></td></tr>
	<tr>
	<td>"._AM_ALUMNI_SCURL."</td><td><input type=\"text\" name=\"scurl\" value=\"$scurl\" size=\"30\" maxlength=\"150\"></td></tr>";
	
	echo "<td>"._AM_ALUMNI_SCPHOTO."  </td><td><select name=\"scphoto\" onchange=\"showimage2()\">"; 
	 
	$none =  _AM_ALUMNI_NONE; 
	$rep2 = XOOPS_ROOT_PATH."/modules/$mydirname/images/schools";
	$handle2=opendir($rep2);
	while ($file2 = readdir($handle2)) {
		$filelist2[] = $file2;
	}
	asort($filelist2);
	while (list ($key, $file2) = each ($filelist2)) {
		if (!ereg(".gif|.jpg|.png",$file2)) {
			if ($file2 == "." || $file2 == "..") $a=1;
			echo "<option value=$file2 select>$none</option>";
		} else {
			if ($file2 == $scphoto) {
				echo "<option value=$file2 selected>$file2</option>";
			} else {
				echo "<option value=$file2>$file2</option>";
			}
		}
	}
	echo "</select>&nbsp;&nbsp;<img src=\"".XOOPS_URL."/modules/$mydirname/images/schools/$scphoto\" name=\"scphoto\" align=\"middle\"> </td></tr><tr><td>&nbsp;</td><td>"._AM_ALUMNI_REPIMGCAT." /modules/$mydirname/images/schools/</td></tr>";
	
	
	if ($xoopsModuleConfig["".$mydirname."_classm"] = "ordre") {
		echo "<tr><td>"._AM_ALUMNI_ORDER." </td><td><input type=\"text\" name=\"ordre\" size=\"4\" value=\"$ordre\"></td></tr>";
	} else {
		echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">";
	}
	echo "</table><P>";
	echo "<input type=\"hidden\" name=\"cidd\" value=\"$cat\">"
	    ."<input type=\"hidden\" name=\"op\" value=\"AlumniModCatS\">"
	    ."<table border=\"0\"><tr><td>"
	    ."<input type=\"submit\" value=\""._AM_ALUMNI_SAVMOD."\"></form></td><td>"
	    ."<form action=\"category.php\" method=\"post\">"
	    ."<input type=\"hidden\" name=\"cid\" value=\"$cat\">"
	    ."<input type=\"hidden\" name=\"op\" value=\"AnoncesDelCat\">"
	    ."<input type=\"submit\" value=\""._AM_ALUMNI_DEL."\"></form></td></tr></table>";

    echo "</fieldset><br />"; 
	xoops_cp_footer();
}
	

#  function AlumniModCatS
#####################################################
function AlumniModCatS($cidd, $cid, $img, $scphoto, $title, $scaddress, $scaddress2, $sccity, $scstate, $sczip, $scphone, $scfax, $scmotto, $scurl, $ordre)
{
    global $xoopsDB,$xoopsConfig, $myts, $mydirname;
	
	$title = $myts->htmlSpecialChars($title);
	$scaddress = $myts->htmlSpecialChars($scaddress);
	$scaddress2 = $myts->htmlSpecialChars($scaddress2);
	$sccity = $myts->htmlSpecialChars($sccity);
	$scstate = $myts->htmlSpecialChars($scstate);
	$sczip = $myts->htmlSpecialChars($sczip);
	$scphone = $myts->htmlSpecialChars($scphone);
	$scfax = $myts->htmlSpecialChars($scfax);
	$scmotto = $myts->htmlSpecialChars($scmotto);
	$scurl = $myts->htmlSpecialChars($scurl);
	
	$xoopsDB->query("update ".$xoopsDB->prefix("alumni_categories")." set title='$title', scaddress='$scaddress', scaddress2='$scaddress2', sccity='$sccity', scstate='$scstate', sczip='$sczip', scphone='$scphone', scfax='$scfax', scmotto='$scmotto', scurl='$scurl', pid='$cid', img='$img', scphoto='$scphoto', ordre='$ordre' where cid=$cidd");
	
	redirect_header("map.php",1,_AM_ALUMNI_CATSMOD);
	exit();
}

	
	#  function AlumniAddCat
#####################################################
function AlumniAddCat($title, $scaddress, $scaddress2, $sccity, $scstate, $sczip, $scphone, $scfax, $scmotto, $scurl, $cid, $img, $scphoto, $ordre) 
{
    global $xoopsDB, $xoopsConfig, $myts;

	$title = $myts->htmlSpecialChars($title);
	$scaddress = $myts->htmlSpecialChars($scaddress);
	$scaddress2 = $myts->htmlSpecialChars($scaddress2);
	$sccity = $myts->htmlSpecialChars($sccity);
	$scstate = $myts->htmlSpecialChars($scstate);
	$sczip = $myts->htmlSpecialChars($sczip);
	$scphone = $myts->htmlSpecialChars($scphone);
	$scfax = $myts->htmlSpecialChars($scfax);
	$scmotto = $myts->htmlSpecialChars($scmotto);
	$scurl = $myts->htmlSpecialChars($scurl);
	
	if ($title == "") {
		$title = "! ! ? ! !";
	}
	
	$xoopsDB->query("insert into ".$xoopsDB->prefix("alumni_categories")." values (NULL, '$cid', '$title', '$scaddress', '$scaddress2', '$sccity', '$scstate', '$sczip', '$scphone', '$scfax', '$scmotto', '$scurl', '$img', '$scphoto', '$ordre')");
	
	redirect_header("map.php",1,_AM_ALUMNI_CATADD);
	exit();
}



#  function AnoncesDelCat
#####################################################
function AnoncesDelCat($cid, $ok=0) {
    global $xoopsDB, $xoopsConfig, $xoopsModule;

    if(intval($ok)==1) {
    	$xoopsDB =& Database::getInstance();
	    $xoopsDB->queryf("delete from ".$xoopsDB->prefix("alumni_categories")." where cid=$cid or pid=$cid");
	    $xoopsDB->queryf("delete from ".$xoopsDB->prefix("alumni_listing")." where cid=$cid");

		redirect_header("map.php",1,_ALUMNI_CATDEL);
		exit();   
    } else {
		xoops_cp_header();
		OpenTable();
		echo "<br /><center><b>"._AM_ALUMNI_SURDELCAT."</b><br /><br />";
		echo "[ <a href=\"category.php?op=AnoncesDelCat&cid=$cid&ok=1\">"._AM_ALUMNI_OUI."</a> | <a href=\"map.php\">"._AM_ALUMNI_NON."</a> ]<br /><br />";
		CloseTable();
		xoops_cp_footer();
	}	
}


#####################################################
foreach ($_POST as $k => $v) {
	${$k} = $v;
}

$ok = isset( $_GET['ok'] ) ? $_GET['ok'] : '' ;

if(!isset($_POST['cid']) && isset($_GET['cid']) ) {
	$cid = $_GET['cid'] ;
}
if(!isset($_POST['op']) && isset($_GET['op']) ) {
	$op = $_GET['op'] ;
}

switch ($op) {

    case "AlumniNewCat":
    AlumniNewCat($cid);
    break;

    case "AlumniAddCat":
    AlumniAddCat( $title, $scaddress, $scaddress2, $sccity, $scstate, $sczip, $scphone, $scfax, $scmotto, $scurl, $cid, $img, $scphoto, $ordre);
    break;

    case "AnoncesDelCat":
    AnoncesDelCat($cid, $ok);
    break;

    case "AlumniModCat":
    AlumniModCat($cid);
    break;

    case "AlumniModCatS":
    AlumniModCatS($cidd, $cid, $img, $scphoto, $title, $scaddress, $scaddress2, $sccity, $scstate, $sczip, $scphone, $scfax, $scmotto, $scurl, $ordre);
    break;

    default:
    Index();
    break;

}
?>