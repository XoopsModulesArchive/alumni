<?php
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
include_once (XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");

$mytree = new XoopsTree($xoopsDB->prefix("alumni_categories"),"cid","pid");
global $mytree, $xoopsDB, $xoopsModuleConfig, $mydirname;
xoops_cp_header();
include( './mymenu.php' ) ;
echo "<fieldset style='padding: 5px;'><legend style='font-weight: bold; color: #900;'>". _AM_ALUMNI_GESTCAT . "</legend>"; 
echo "<br /><a href=\"category.php?op=AlumniNewCat&cid=0\"><img src=\"".XOOPS_URL."/modules/$mydirname/images/plus.gif\" border=0 width=10 height=10  alt=\""._AM_ALUMNI_ADDSUBCAT."\"></a> "._AM_ALUMNI_ADDCATPRINC."<br /><br />";

$mytree->makeMapSelBox("title", "".$xoopsModuleConfig["".$mydirname."_csortorder"]."");

echo "<br /><hr />";
echo "<p>"._AM_ALUMNI_HELP1." </p>";

if ($xoopsModuleConfig["".$mydirname."_csortorder"] == "ordre") {
	echo "<p>"._AM_ALUMNI_HELP2." </p>";
}
echo "<br /></fieldset><br />"; 
xoops_cp_footer();
?>