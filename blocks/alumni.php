<?php
//  -----------------------------------------------------------------------  //
//                           Jobs for Xoops 2.0x                             //
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

function alumni_show()
{
	global $xoopsDB, $xoopsConfig, $ynprice, $myts;
	$myts =& MyTextSanitizer::getInstance();
	
	$block = array();
	$block['title'] = _MB_ALUMNI_TITLE;
	
	//from MyAds 2.05beta7
	// read configs from xoops_config directly
	$rs = $xoopsDB->query( "SELECT conf_value FROM ".$xoopsDB->prefix('config')." WHERE conf_name='newclassifieds'" ) ;
	while( list( $val ) = $xoopsDB->fetchRow( $rs ) ) {
		$newclassifieds = $val ;
	}
	
	$query = "select lid, name, mname, lname, school FROM ".$xoopsDB->prefix("alumni_listing")." WHERE valid='Yes' ORDER BY date DESC LIMIT $newclassifieds";
	$result=$xoopsDB->query($query);

	while(list($lid, $name, $mname, $lname, $school)=$xoopsDB->fetchRow($result)) {
		$name = $myts->htmlSpecialChars($name);
		$mname = $myts->htmlSpecialChars($mname);
		$lname = $myts->htmlSpecialChars($lname);
		$school = $myts->htmlSpecialChars($school);

		if ( !XOOPS_USE_MULTIBYTES ) {
			if (strlen($name) >= 14) {
				$name = substr($name,0,18)."...";
			}
		}
	$a_item['name'] = $name;
	$a_item['mname'] = $mname;
	$a_item['lname'] = $lname;
	$a_item['school'] = $school;
    	$a_item['link'] = "<a href=\"".XOOPS_URL."/modules/alumni/index.php?pa=viewlistings&amp;lid=$lid\">$name $mname $lname</a>";
    	$block['items'][] = $a_item;
    }
    
	$block['link'] = "<a href=\"".XOOPS_URL."/modules/alumni/\">"._MB_ALUMNI_ALLANN2."</a>";
    return $block;
}
?>
