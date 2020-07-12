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
include("header.php");

xoops_header();
$lid = intval($_GET['lid']);

global $xoopsUser, $xoopsConfig, $xoopsTheme, $xoopsDB, $xoops_footer, $xoopsLogger;
$currenttheme = $xoopsConfig['theme_set'] ;

$result=$xoopsDB->query("select photo2 FROM ".$xoopsDB->prefix("alumni_listing")." WHERE lid = ".mysql_real_escape_string($lid)."");
$recordexist = $xoopsDB->getRowsNum($result);

if ($recordexist)
{     
	list($photo2)=$xoopsDB->fetchRow($result);
	echo "<center><img src=\"now_photo/$photo2\" border=0></center>";
}	

echo "<center><br /><br /><table><tr><td><a href=#  onClick='window.close()'>"._ALUMNI_CLOSEF."</a></td></tr></table></center>";

xoops_footer();
?>