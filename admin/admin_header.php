<?php

include("../../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include(XOOPS_ROOT_PATH."/include/cp_functions.php");

	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	if( ! preg_match( '/^(\D+)(\d*)$/' , $mydirname , $regs ) ) echo ( "invalid dirname: " . htmlspecialchars( $mydirname ) ) ;

	require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;
if ( $xoopsUser ) {
	$xoopsModule = XoopsModule::getByDirname("$mydirname");
	if ( !$xoopsUser->isAdmin($xoopsModule->mid()) ) { 
		redirect_header(XOOPS_URL."/",3,_NOPERM);
		exit();
	}
} else {
	redirect_header(XOOPS_URL."/",3,_NOPERM);
	exit();
}

$myts =& MyTextSanitizer::getInstance();

?>
