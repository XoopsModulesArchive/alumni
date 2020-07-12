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
function alumni_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	
	$sql = "SELECT lid, usid, name, mname, lname, school, year, date FROM ".$xoopsDB->prefix("alumni_listing")." WHERE valid='Yes' AND date<=".time()."";
	if ( $userid != 0 ) {
		$sql .= " AND usid=".$userid." ";
	} 
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((name LIKE '%$queryarray[0]%' OR mname LIKE '%$queryarray[0]%' OR lname LIKE '%$queryarray[0]%' OR school LIKE '%$queryarray[0]%' OR year LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(name LIKE '%$queryarray[$i]%' OR mname LIKE '%$queryarray[$i]%' OR lname LIKE '%$queryarray[$i]%' OR school LIKE '%$queryarray[$i]%' OR year LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY date DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
 	while($myrow = $xoopsDB->fetchArray($result)){
		$ret[$i]['image'] = "images/cat/default.gif";
		$ret[$i]['link'] = "index.php?pa=viewlistings&amp;lid=".$myrow['lid']."";
		$ret[$i]['title'] = $myrow['name']." ".$myrow['mname']." ".$myrow['lname']."   ---   ".$myrow['school']."   
		---   ".$myrow['year'];
		$ret[$i]['time'] = $myrow['date'];
		$ret[$i]['uid'] = $myrow['usid'];
		$i++;
	}
	return $ret;
}
?>