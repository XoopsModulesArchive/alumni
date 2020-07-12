<?php
function b_waiting_alumni(){
	$xoopsDB =& Database::getInstance();
	$block = array();

	$result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("alumni_listing")." WHERE valid='No'");
	if ( $result ) {
		$block['adminlink'] = XOOPS_URL."/modules/alumni/admin/index.php";
		list($block['pendingnum']) = $xoopsDB->fetchRow($result);
		$block['lang_linkname'] = _PI_WAITING_SUBMITTED ;
	}

	return $block;
}
?>