<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.0x                           //
//                  By John Mordo from the myAds 2.04 Module                 //
//                    All Original credits left below this                   //
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
if (!$gperm_handler->checkRight("".$mydirname."_view", $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL."/index.php", 3, _NOPERM);
    exit();
}
if (!$gperm_handler->checkRight("".$mydirname."_premium", $perm_itemid, $groups, $module_id)) {
    $prem_perm = "0";
} else {
    $prem_perm = "1";
}

include(XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php");
include(XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php");
$mytree = new XoopsTree($xoopsDB->prefix("".$mydirname."_categories"),"cid","pid");

/**
 *  function index
 **/
function index()
{
	global $xoopsDB, $xoopsTpl, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsUser, $myts, $mytree, $meta, $mydirname;
	$GLOBALS['xoopsOption']['template_main'] = "".$mydirname."_index.html";
	include(XOOPS_ROOT_PATH."/modules/$mydirname/class/nav.php");
	include XOOPS_ROOT_PATH."/header.php";

	$xoopsTpl->assign('xmid', $xoopsModule->getVar('mid'));
	$xoopsTpl->assign('add_from', _ALUMNI_ADDFROM." ".$xoopsConfig['sitename']);
	$xoopsTpl->assign('add_from_title', _ALUMNI_ADDFROM );
	$xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);
	$xoopsTpl->assign('add_from_title', _ALUMNI_ADDFROM );
	$xoopsTpl->assign('class_of', _ALUMNI_CLASSOF );
	$xoopsTpl->assign('front_intro', _ALUMNI_FINTRO );
	$xoopsTpl->assign('search_listings', _ALUMNI_SEARCH_LISTINGS );
	$xoopsTpl->assign('all_words', _ALUMNI_ALL_WORDS );
	$xoopsTpl->assign('any_words', _ALUMNI_ANY_WORDS );
	$xoopsTpl->assign('exact_match', _ALUMNI_EXACT_MATCH );

	if ($xoopsModuleConfig["".$mydirname."_moderated"] == '1') {
		$result = $xoopsDB->query("select  COUNT(*)  FROM ".$xoopsDB->prefix("".$mydirname."_listing")." WHERE valid='No'");
		list($propo) = $xoopsDB->fetchRow($result);
		$xoopsTpl->assign('moderated', true);
		
	    if ($xoopsUser) {
			if ($xoopsUser->isAdmin()) {
				$xoopsTpl->assign('admin_block', _ALUMNI_ADMINCADRE);
				if($propo == 0) {
					$xoopsTpl->assign('confirm_ads', _ALUMNI_NO_ALUM);
				} else {
					$xoopsTpl->assign('confirm_ads', _ALUMNI_THEREIS." $propo  "._ALUMNI_WAIT."<br /><a href=\"admin/index.php\">"._ALUMNI_SEEIT."</a>");
				}
			}
		}
	}

	$sql = "SELECT cid, title, img, scphoto FROM ".$xoopsDB->prefix("".$mydirname."_categories")." WHERE pid = 0 ";

$categories = alumni_MygetItemIds("".$mydirname."_view");
if(is_array($categories) && count($categories) > 0) {
	$sql .= " AND cid IN (".implode(',', $categories).") ";
} else {
	// User can't see any category
	redirect_header(XOOPS_URL.'/index.php', 3, _NOPERM);
	exit();	
}
$sql .= 'ORDER BY '.$xoopsModuleConfig["".$mydirname."_csortorder"].'';

$result = $xoopsDB->query($sql);

$count = 1;
$content = '';
while($myrow = $xoopsDB->fetchArray($result)) {
	$title = $myts->htmlSpecialChars($myrow['title']);

	if ($myrow['img'] && $myrow['img'] != 'http://'){

		$cat_img = $myts->htmlSpecialChars($myrow['img']);
		$img = "<a href=\"index.php?pa=view&amp;cid=".$myrow['cid']."\"><img src='".XOOPS_URL."/modules/$mydirname/images/cat/".$cat_img."' align='middle' alt='' /></a>";

	} else {
		$img = '';
	}
	$totallisting = getTotalItems($myrow['cid'], 1);
	$content .= $title.' ';

	// get child category objects
	$arr = array();
	if(in_array($myrow['cid'], $categories)) {
		$arr = $mytree->getFirstChild($myrow['cid'], "".$xoopsModuleConfig["".$mydirname."_csortorder"]."");
		$space = 0;
		$chcount = 0;
		$subcategories = '';
		foreach($arr as $ele){
			if(in_array($ele['cid'], $categories)) {
				$chtitle=$myts->htmlSpecialChars($ele['title']);
				if ($chcount>$xoopsModuleConfig["".$mydirname."_nbsouscat"]) {
					$subcategories .= ', ...';
					break;
				}
				if ($space>0) {
					$subcategories .= '<br />';
				}
				$subcategories .= "<a href=\"".XOOPS_URL."/modules/$mydirname/index.php?pa=view&amp;cid=".$ele['cid']."\">".$chtitle."</a>";
				$space++;
				$chcount++;
				$content .= $ele['title'].' ';
			}
		}

		$xoopsTpl->append('categories', array('image' => $img, 'id' => $myrow['cid'], 'title' => $myts->htmlSpecialChars($myrow['title']), 'new' => categorynewgraphic($myrow['cid']), 'subcategories' => $subcategories, 'totallisting' => $totallisting, 'count' => $count));
		$count++;
	}
}
	$cat_perms = "";
if(is_array($categories) && count($categories) > 0) {
	$cat_perms .= ' AND cid IN ('.implode(',', $categories).') ';
}
$sql = "SELECT COUNT(*) FROM ".$xoopsDB->prefix("".$mydirname."_listing")." WHERE valid=Yes $cat_perms";

	$xoopsTpl->assign('cat_count', $count-1);
	list($alumni) = $xoopsDB->fetchRow($xoopsDB->query("select  COUNT(*)  FROM ".$xoopsDB->prefix("".$mydirname."_listing")." WHERE valid='Yes'"));
	list($catt) = $xoopsDB->fetchRow($xoopsDB->query("select  COUNT(*)  FROM ".$xoopsDB->prefix("".$mydirname."_categories").""));
	
	$xoopsTpl->assign('total_listings', _ALUMNI_ACTUALY." $alumni "._ALUMNI_LISTINGS." "._ALUMNI_INCAT." $catt "._ALUMNI_CAT2);
	if ($xoopsModuleConfig["".$mydirname."_moderated"] == '1') {
		$xoopsTpl->assign('total_confirm', _ALUMNI_AND." $propo "._ALUMNI_WAIT3);
	}
	$xoopsTpl->assign('title', $title);
	$result=$xoopsDB->query("select lid, cid, name, mname, lname, school, year, studies, date, town, valid, photo, photo2, view FROM ".$xoopsDB->prefix("".$mydirname."_listing")." WHERE valid='Yes' ORDER BY date DESC LIMIT ".$xoopsModuleConfig["".$mydirname."_newalumni"]."");
	if ($result){
		$xoopsTpl->assign('last_head', _ALUMNI_THE." ".$xoopsModuleConfig["".$mydirname."_newalumni"]." "._ALUMNI_LASTADD);
		$xoopsTpl->assign('last_head_name', _ALUMNI_NAME2);
		$xoopsTpl->assign('last_head_school', _ALUMNI_SCHOOL2);
		$xoopsTpl->assign('last_head_year', _ALUMNI_YEAR);
		$xoopsTpl->assign('last_head_date', _ALUMNI_DATE);
		$xoopsTpl->assign('last_head_local', _ALUMNI_LOCAL2);
		$xoopsTpl->assign('last_head_views', _ALUMNI_VIEW);
		$xoopsTpl->assign('last_head_photo', _ALUMNI_PHOTO);
		$xoopsTpl->assign('last_head_photo2', _ALUMNI_PHOTO2);

		$rank = 1;

		while(list($lid, $cid, $name, $mname, $lname, $school, $year, $studies, $date, $town, $valid, $photo, $photo2, $vu)=$xoopsDB->fetchRow($result)) {
			$name = $myts->htmlSpecialChars($name);
			$mname = $myts->htmlSpecialChars($mname);
			$lname = $myts->htmlSpecialChars($lname);
			$school = $myts->htmlSpecialChars($school);
			$year = $myts->htmlSpecialChars($year);
			$studies = $myts->htmlSpecialChars($studies);
			$town = $myts->htmlSpecialChars($town);
			$a_item = array();
			
			$useroffset = "";
	    	if($xoopsUser) {
				$timezone = $xoopsUser->timezone();
				if(isset($timezone)) {
					$useroffset = $xoopsUser->timezone();
				} else {
					$useroffset = $xoopsConfig['default_TZ'];
				}
			}

			$date = ($useroffset*3600) + $date;
			$date = formatTimestamp($date,"s");

			if ($xoopsUser) {
				if ($xoopsUser->isAdmin()) {
					$a_item['admin'] = "<a href='admin/index.php?op=Modify&amp;lid=$lid'><img src='images/modif.gif' border=0 alt=\""._ALUMNI_MODADMIN."\" /></a>";
				}
			}

			$a_item['name'] = "<a href='index.php?pa=viewlistings&amp;lid=$lid'><b>$name&nbsp;$mname&nbsp;$lname</b></a>";
			$a_item['school'] = $school;
			$a_item['year'] = $year;
			$a_item['studies'] = $studies;
			$a_item['date'] = $date;
			$a_item['local'] = '';
			if ($town) {
				$a_item['local'] .= $town;
			}
			
			if ($photo) {
				$a_item['photo'] = "<a href=\"javascript:CLA('display-image.php?lid=$lid')\"><img src=\"images/photo.gif\" border=\"0\" width=\"15\" height=\"11\" alt='"._ALUMNI_IMGPISP."' /></a>";
			}
			
			if ($photo2) {
				$a_item['photo2'] = "<a href=\"javascript:CLA('display-image2.php?lid=$lid')\"><img src=\"images/photo.gif\" border=\"0\" width=\"15\" height=\"11\" alt='"._ALUMNI_IMGPISP."' /></a>";
			}
			
			$a_item['views'] = $vu;

			$rank++;
			$xoopsTpl->append('items', $a_item);
		}
	}
}
	
/**
 *  function view (categories)
 **/
function view($cid=0,$min=0,$orderby,$show=0)
{
    global $xoopsDB, $xoopsTpl, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $mytree, $imagecat, $meta, $mydirname, $main_lang;
    $GLOBALS['xoopsOption']['template_main'] = "".$mydirname."_category.html";
//	include(XOOPS_ROOT_PATH."/modules/$mydirname/class/nav.php");
	include XOOPS_ROOT_PATH."/header.php";

	$default_sort = $xoopsModuleConfig["".$mydirname."_lsortorder"];

	$cid = (intval($cid) > 0) ? intval($cid) : 0 ;
	$min = (intval($min) > 0) ? intval($min) : 0 ;
	$show = (intval($show) > 0 ) ? intval($show) : $xoopsModuleConfig["".$mydirname."_per_page"] ;
	$max = $min + $show;
	$orderby = (isset($orderby)) ? alumni_convertorderbyin($orderby) : $default_sort ;

	$xoopsTpl->assign('add_from', _ALUMNI_ADDFROM." ".$xoopsConfig['sitename']);
	$xoopsTpl->assign('add_from_title', _ALUMNI_ADDFROM );
	$xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);
	$xoopsTpl->assign('add_listing', "<a href='addlisting.php?cid=$cid'>"._ALUMNI_ADDLISTING2."</a>");

	$categories = alumni_MygetItemIds('alumni_view');
	if(is_array($categories) && count($categories) > 0) {
	if(!in_array($cid, $categories)) {
		redirect_header(XOOPS_URL."/modules/$mydirname/index.php", 3, _NOPERM);
		exit();
	}
	} else {
	// User can't see any category
	redirect_header(XOOPS_URL.'/index.php', 3, _NOPERM);
	exit();
	}

$pathstring = "<a href='index.php'>".$xoopsModule->name()."</a>";
$pathstring .= $mytree->getNicePathFromId($cid, 'title', 'index.php?pa=view');
$xoopsTpl->assign('module_name', $xoopsModule->getVar('name'));
$xoopsTpl->assign('category_path', $pathstring);
$xoopsTpl->assign('category_id', $cid);

$countresult=$xoopsDB->query("select COUNT(*) FROM ".$xoopsDB->prefix("".$mydirname."_listing")." where  cid=".mysql_real_escape_string($cid)." AND valid='Yes'");
			list($trow) = $xoopsDB->fetchRow($countresult);
			$trows = $trow;

		$result = $xoopsDB->query("select cid, pid, title, scaddress, scaddress2, sccity, scstate, sczip, scphone, scfax, scmotto, scurl, scphoto from ".$xoopsDB->prefix("".$mydirname."_categories")." where  cid=".mysql_real_escape_string($cid)."");

	list($cid, $pid, $title, $scaddress, $scaddress2, $sccity, $scstate, $sczip, $scphone, $scfax, $scmotto, $scurl, $scphoto) = $xoopsDB->fetchRow($result);
	
	$xoopsTpl->assign('xoops_pagetitle',$title);

$arr = array();
$arr = $mytree->getFirstChild($cid, "title");
if ( count($arr) > 0 ) {
	$scount = 1;
	foreach($arr as $ele){
		if(in_array($ele['cid'], $categories)) {
			$sub_arr = array();
			$sub_arr = $mytree->getFirstChild($ele['cid'], 'title');
			$space = 0;
			$chcount = 0;
			$infercategories = "";
			foreach($sub_arr as $sub_ele){

if (in_array($sub_ele['cid'], $categories)) {

				$chtitle = $myts->htmlSpecialChars($sub_ele['title']);
				
				if ($chcount>5){
					$infercategories .= "...";
					break;
				}
				if ($space>0) {
					$infercategories .= ", ";
				}
				$infercategories .= "<a href=\"".XOOPS_URL."/modules/$mydirname/index.php?pa=view&amp;cid=".$sub_ele['cid']."\">".$chtitle."</a>";

				 $infercategories .= "&nbsp;(".getTotalItems($sub_ele['cid']).")";
				 $infercategories .= "&nbsp;".categorynewgraphic($sub_ele['cid'])."";
				$space++;
				$chcount++;
			}
		}
			
			$xoopsTpl->append('subcategories', array('title' => $myts->htmlSpecialChars($ele['title']), 'id' => $ele['cid'], 'infercategories' => $infercategories, 'totallinks' => getTotalItems($ele['cid'], 1), 'count' => $scount, 'new' =>  "&nbsp;".categorynewgraphic($ele['cid']).""));
			$scount++;

	$xoopsTpl->assign('lang_subcat', constant($main_lang."_AVAILAB"));
	$xoopsTpl->assign('top_scphoto', "<img src='".XOOPS_URL."/modules/$mydirname/images/schools/$scphoto' align='middle' alt='' />");
	$xoopsTpl->assign('title', $title);
	$xoopsTpl->assign('scaddress', $scaddress);
	$xoopsTpl->assign('scaddress2', $scaddress2);
	$xoopsTpl->assign('sccity', $sccity);
	$xoopsTpl->assign('scstate', $scstate);
	$xoopsTpl->assign('sczip', $sczip);
	$xoopsTpl->assign('scphone', $scphone);
	$xoopsTpl->assign('scfax', $scfax);
	$xoopsTpl->assign('scmotto', $scmotto);
	$xoopsTpl->assign('scurl', $scurl);
	$xoopsTpl->assign('nav_main', "<a href=\"index.php\">"._ALUMNI_MAIN."</a>");
	$xoopsTpl->assign('nav_sub', $subcats);
	$xoopsTpl->assign('nav_subcount', $nbe);
	$xoopsTpl->assign('head_scphone', _ALUMNI_SCPHONE);
	$xoopsTpl->assign('head_scfax', _ALUMNI_SCFAX);
	$xoopsTpl->assign('web', _ALUMNI_WEB);
	$xoopsTpl->assign('school_name', $school_name);

		}
	}
}
	$pagenav = '';
if ($trows > "0") {

	
    		$xoopsTpl->assign('last_head', _ALUMNI_THE." ".$xoopsModuleConfig["".$mydirname."_newalumni"]." "._ALUMNI_LASTADD);
		$xoopsTpl->assign('last_head_name', _ALUMNI_NAME2);
		$xoopsTpl->assign('last_head_mname', _ALUMNI_MNAME);
		$xoopsTpl->assign('last_head_lname', _ALUMNI_LNAME);
		$xoopsTpl->assign('last_head_school', _ALUMNI_SCHOOL);
		$xoopsTpl->assign('class_of', _ALUMNI_CLASSOF);
		$xoopsTpl->assign('last_head_studies', _ALUMNI_STUDIES);
		$xoopsTpl->assign('last_head_occ', _ALUMNI_OCC);
		$xoopsTpl->assign('last_head_activities', _ALUMNI_ACTIVITIES);
		$xoopsTpl->assign('last_head_date', _ALUMNI_DATE);
		$xoopsTpl->assign('last_head_local', _ALUMNI_LOCAL2);
		$xoopsTpl->assign('last_head_views', _ALUMNI_VIEW);
		$xoopsTpl->assign('last_head_photo', _ALUMNI_PHOTO);
		$xoopsTpl->assign('last_head_photo2', _ALUMNI_PHOTO2);
		$xoopsTpl->assign('cat', $cid);
		$xoopsTpl->assign('min', $min);
		$rank = 1;
	$cat_perms = "";
	if(is_array($categories) && count($categories) > 0) {
	$cat_perms .= ' AND cid IN ('.implode(',', $categories).') ';
	}

 //       $xoopsTpl->assign('school_listings', _ALUMNI_ACTUALY."&nbsp;$nbe&nbsp;$title "._ALUMNI_LISTINGS);
//	    $newalumni = $xoopsModuleConfig["".$mydirname."_newalumni"];

    $sql="select lid, cid, name, mname, lname, school, year, studies, activities, occ, date, town, valid, photo, photo2, view from ".$xoopsDB->prefix("".$mydirname."_listing")." where  valid='Yes' AND cid=".mysql_real_escape_string($cid)." $cat_perms order by $orderby";
$result1=$xoopsDB->query($sql,$show,$min);
	if ($trows > "1") {

	$xoopsTpl->assign('show_nav', true);
        $orderbyTrans = alumni_convertorderbytrans($orderby);
        $xoopsTpl->assign('lang_sortby', constant($main_lang."_SORTBY"));
        $xoopsTpl->assign('lang_name', constant($main_lang."_NAME2"));
	$xoopsTpl->assign('lang_nameatoz', constant($main_lang."_NAMEATOZ"));
	$xoopsTpl->assign('lang_nameztoa', constant($main_lang."_NAMEZTOA"));
        $xoopsTpl->assign('lang_date', constant($main_lang."_DATE"));
	$xoopsTpl->assign('lang_dateold', constant($main_lang."_DATEOLD"));
	$xoopsTpl->assign('lang_datenew', constant($main_lang."_DATENEW"));
        $xoopsTpl->assign('lang_cursortedby', sprintf(constant($main_lang."_CURSORTEDBY"), alumni_convertorderbytrans($orderby)));
		}

	while(list($lid, $cid, $name, $mname, $lname, $school, $year, $studies, $activities, $occ, $date, $town, $valid, $photo, $photo2, $vu)=$xoopsDB->fetchRow($result1)) {

		$a_item = array();
		$name = $myts->htmlSpecialChars($name);
		$mname = $myts->htmlSpecialChars($mname);
		$lname = $myts->htmlSpecialChars($lname);
		$school = $myts->htmlSpecialChars($school);
		$year = $myts->htmlSpecialChars($year);
		$studies = $myts->htmlSpecialChars($studies);
		$activities = $myts->htmlSpecialChars($activities);
		$occ = $myts->htmlSpecialChars($occ);
		$town = $myts->htmlSpecialChars($town);

		$useroffset = "";

		$newcount = $xoopsModuleConfig["".$mydirname."_countday"];
		$startdate = (time()-(86400 * $newcount));
		if ($startdate < $date) {
		$newitem = "<img src=\"".XOOPS_URL."/modules/$mydirname/images/newred.gif\" />";
		$a_item['new'] = $newitem;
			}
		if($xoopsUser) {
			$timezone = $xoopsUser->timezone();
				if(isset($timezone)) {
					$useroffset = $xoopsUser->timezone();
				} else {
					$useroffset = $xoopsConfig['default_TZ'];
				}
			}
			$date = ($useroffset*3600) + $date;
			$date = formatTimestamp($date,"s");
			if ($xoopsUser) {
				if ($xoopsUser->isAdmin()){
					$a_item['admin'] = "<a href='admin/index.php?op=Modify&amp;lid=$lid'><img src='images/modif.gif' border=0 alt=\""._ALUMNI_MODADMIN."\" /></a>";
				}
			}

			$a_item['name'] = "<a href='index.php?pa=viewlistings&amp;lid=$lid'><b>$name&nbsp;$mname&nbsp;$lname</b></a>";
			$a_item['school'] = $school;
			$a_item['year'] = $year;
			$a_item['studies'] = $studies;
			$a_item['occ'] = $occ;
			$a_item['activities'] = $activities;
			$a_item['date'] = $date;
			$a_item['local'] = '';
			if ($town) {
				$a_item['local'] .= $town;
			}
					$cat = addslashes($cid);
			if ($photo) {
				$a_item['photo'] = "<a href=\"javascript:CLA('display-image.php?lid=$lid')\"><img src=\"images/photo.gif\" border=\"0\" width=\"15\" height=\"11\" alt='"._ALUMNI_IMGPISP."' /></a>";
			}
			
			if ($photo2) {
				$a_item['photo2'] = "<a href=\"javascript:CLA('display-image2.php?lid=$lid')\"><img src=\"images/photo.gif\" border=\"0\" width=\"15\" height=\"11\" alt='"._ALUMNI_IMGPISP."' /></a>";
			}
			
			$a_item['views'] = $vu;
			$rank++;
			$xoopsTpl->append('items', $a_item);
		}
	
		$cid = intval($_GET['cid']);
	//	$cid = (intval($cid) > 0) ? intval($cid) : 0 ;

		$orderby = alumni_convertorderbyout($orderby);
		
		//Calculates how many pages exist.  Which page one should be on, etc...
    $linkpages = ceil($trows / $show);
    //Page Numbering
    if ($linkpages!=1 && $linkpages!=0) {
       $prev = $min - $show;
       if ($prev>=0) {
            $pagenav .= "<a href='index.php?pa=view&cid=$cid&min=$prev&orderby=$orderby&show=$show'><b><u>&laquo;</u></b></a> ";
        }
        $counter = 1;
        $currentpage = ($max / $show);
        while ( $counter<=$linkpages ) {
            $mintemp = ($show * $counter) - $show;
            if ($counter == $currentpage) {
                $pagenav .= "<b>($counter)</b> ";
            } else {
                $pagenav .= "<a href='index.php?pa=view&cid=$cid&min=$mintemp&orderby=$orderby&show=$show'>$counter</a> ";
            }
            $counter++;
        }
        if ( $trows >$max ) {
            $pagenav .= "<a href='index.php?pa=view&cid=$cid&min=$max&orderby=$orderby&show=$show'>";
            $pagenav .= "<b><u>&raquo;</u></b></a>";
        }
    }
}

$xoopsTpl->assign('nav_page', $pagenav);
}


/**
 *  function viewlistings
 **/
function viewlistings($lid=0)
{
    global $xoopsDB, $xoopsConfig, $xoopsModuleConfig, $xoopsTpl, $xoopsUser, $myts, $meta, $mydirname;

	$GLOBALS['xoopsOption']['template_main'] = "".$mydirname."_item.html";
	include XOOPS_ROOT_PATH."/header.php";
	// add for Nav by Tom
	include(XOOPS_ROOT_PATH."/modules/$mydirname/class/nav.php");

    $result=$xoopsDB->query("select lid, cid, name, mname, lname, school, year, studies, activities, extrainfo, occ, date, email, submitter, usid, town, valid, photo, photo2, view FROM ".$xoopsDB->prefix("".$mydirname."_listing")." WHERE lid = ".mysql_real_escape_string($lid)."");
    $recordexist = $xoopsDB->getRowsNum($result);

	$xoopsTpl->assign('add_from', _ALUMNI_ADDFROM." ".$xoopsConfig['sitename']);
	$xoopsTpl->assign('add_from_title', _ALUMNI_ADDFROM );
	$xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);
	$xoopsTpl->assign('class_of', _ALUMNI_CLASSOF );
	$xoopsTpl->assign('ad_exists', $recordexist);
	$count = 0;
	$x=0;
	$i=0;
	
	$request2 = $xoopsDB->query("select cid from ".$xoopsDB->prefix("".$mydirname."_listing")." where  lid=".mysql_real_escape_string($lid)."");
	list($cid) = $xoopsDB->fetchRow($request2);

	$request = $xoopsDB->query("select cid, pid, title from ".$xoopsDB->prefix("".$mydirname."_categories")." where  cid=".mysql_real_escape_string($cid)."");
	list($ccid, $pid, $title) = $xoopsDB->fetchRow($request);

	$title = $myts->htmlSpecialChars($title);
	$varid[$x]=$ccid;
	$varnom[$x]=$title;

	list($nbe) = $xoopsDB->fetchRow($xoopsDB->query("select COUNT(*) FROM ".$xoopsDB->prefix("".$mydirname."_listing")." where valid='Yes' AND cid=".mysql_real_escape_string($cid).""));
	
	if($pid!=0) {
		$x=1;	
		while($pid!=0) {
			$request2 = $xoopsDB->query("select cid, pid, title from ".$xoopsDB->prefix("".$mydirname."_categories")." where cid=".mysql_real_escape_string($pid)."");
			list($ccid, $pid, $title) = $xoopsDB->fetchRow($request2);
			
			$title = $myts->htmlSpecialChars($title);
			
			$varid[$x]=$ccid;
			$varnom[$x]=$title;
			$x++;
		}
		$x=$x-1;
	} 
	
	$subcats = '';
	while($x!=-1) {
		$subcats .= " &raquo; <a href=\"index.php?pa=view&amp;cid=".$varid[$x]."\">".$varnom[$x]."</a>";
		$x=$x-1;
	}
	$xoopsTpl->assign('nav_main', "<a href=\"index.php\">"._ALUMNI_MAIN."</a>");
	$xoopsTpl->assign('nav_sub', $subcats);
	$xoopsTpl->assign('nav_subcount', $nbe);
/* ---- /nav ----- */

    if ($recordexist) {
		list($lid, $cid, $name, $mname, $lname, $school, $year, $studies, $activities, $extrainfo, $occ, $date, $email, $submitter, $usid, $town, $valid, $photo, $photo2, $view)=$xoopsDB->fetchRow($result);

		$viewcount_judge = true ;
		$useroffset = "";
		if($xoopsUser) {
			$timezone = $xoopsUser->timezone();
			if(isset($timezone)) {
				$useroffset = $xoopsUser->timezone();
			}else {
				$useroffset = $xoopsConfig['default_TZ'];
			}
			//	Specification for Japan: view count up judge
			if (($xoopsUser->getVar("uid") == 1)||($xoopsUser->getVar("uid") ==$usid)) {
					$viewcount_judge = false ;
			}
		}
		//	Specification for Japan: view count up judge
		if ($viewcount_judge == true ){
			$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("".$mydirname."_listing")." SET view=view+1 WHERE lid = '$lid'");
		}
		$date = formatTimestamp($date,'s');
		$name = $myts->htmlSpecialChars($name);
		$mname = $myts->htmlSpecialChars($mname);
		$lname = $myts->htmlSpecialChars($lname);
		$school = $myts->htmlSpecialChars($school);
		$year = $myts->htmlSpecialChars($year);
		$studies = $myts->htmlSpecialChars($studies);
		$activities = $myts->displayTarea($activities,1,1,1);
		$extrainfo = $myts->displayTarea($extrainfo,1,1,1);
		$occ = $myts->htmlSpecialChars($occ);
		$submitter = $myts->htmlSpecialChars($submitter);
		$town = $myts->htmlSpecialChars($town);
	
		$printA = "<a href=\"print.php?op=PrintAlum&amp;lid=".addslashes($lid)."\" target=\"_blank\"><img src=\"images/print.gif\" border=0 alt=\""._ALUMNI_PRINT."\" width=15 height=11 /></a>&nbsp;";
		$mailA = "<a href=\"sendfriend.php?op=SendFriend&amp;lid=$lid\"><img src=\"../$mydirname/images/friend.gif\" border=\"0\" alt=\""._ALUMNI_SENDFRIEND."\" width=\"15\" height=\"11\" /></a>";
		if ($usid > 0) {
			$xoopsTpl->assign('submitter', _ALUMNI_FROM . "<a href='".XOOPS_URL."/userinfo.php?uid=".addslashes($usid)."'>$submitter</a>");
		} else {
			$xoopsTpl->assign('submitter', _ALUMNI_FROM . "$submitter");
		}

		$xoopsTpl->assign('print', "$printA");
		$xoopsTpl->assign('sfriend', "$mailA");
		$xoopsTpl->assign('read', "$view " . _ALUMNI_VIEW2);

		if ($xoopsUser) {
			$calusern = $xoopsUser->getVar("uid", "E");
			if ($usid == $calusern) {
		$xoopsTpl->assign('modify', "<a href=\"modlisting.php?op=ModAlumni&amp;lid=".addslashes($lid)."\">"._ALUMNI_MODIFY."</a>  |  <a href=\"modlisting.php?op=AlumniDel&amp;lid=".addslashes($lid)."\">"._ALUMNI_DELETE."</a>");
		}
			if ($xoopsUser->isAdmin()) {
				$xoopsTpl->assign('admin', "<a href=\"admin/index.php?op=Modify&amp;lid=".addslashes($lid)."\"><img src=\"images/modif.gif\" border=0 alt=\""._ALUMNI_MODADMIN."\" /></a>");
			}
		}

		$xoopsTpl->assign('name', $name);
		$xoopsTpl->assign('mname', $mname);
		$xoopsTpl->assign('lname', $lname);
		$xoopsTpl->assign('school', $school);
		$xoopsTpl->assign('title', $title);
		$xoopsTpl->assign('year', $year);
		$xoopsTpl->assign('studies', $studies);
		$xoopsTpl->assign('name_head', _ALUMNI_NAME2);
		$xoopsTpl->assign('class_of', _ALUMNI_CLASSOF);
		$xoopsTpl->assign('mname_head', _ALUMNI_MNAME);
		$xoopsTpl->assign('lname_head', _ALUMNI_LNAME);
		$xoopsTpl->assign('school_head', _ALUMNI_SCHOOL);
		$xoopsTpl->assign('year_head', _ALUMNI_YEAR);
		$xoopsTpl->assign('studies_head', _ALUMNI_STUDIES);
		$xoopsTpl->assign('local_town', $town);
		$xoopsTpl->assign('local_head', _ALUMNI_LOCAL);
		$xoopsTpl->assign('alumni_mustlogin', _ALUMNI_MUSTLOGIN );
		$xoopsTpl->assign('photo_head', _ALUMNI_GPHOTO );
		$xoopsTpl->assign('photo2_head', _ALUMNI_RPHOTO2 );
		$xoopsTpl->assign('activities', $activities);
		$xoopsTpl->assign('extrainfo', $extrainfo);
		$xoopsTpl->assign('activities_head', _ALUMNI_ACTIVITIES);
		$xoopsTpl->assign('extrainfo_head', _ALUMNI_EXTRAINFO);

		if ($email) {
		$xoopsTpl->assign('contact_head', _ALUMNI_CONTACT);
		$xoopsTpl->assign('contact_email', "<a href=\"contact.php?lid=$lid\">"._ALUMNI_BYMAIL2."</a>");
		}
		$xoopsTpl->assign('contact_occ_head', _ALUMNI_OCC);
		$xoopsTpl->assign('contact_occ', "$occ");

		if ($photo) {
			$xoopsTpl->assign('photo', "<img src=\"grad_photo/$photo\" alt=\"$title\" />");
		}
		if ($photo2) {
		$xoopsTpl->assign('photo2', "<img src=\"now_photo/$photo2\" alt=\"$title\" />");
		}
		$xoopsTpl->assign('date', _ALUMNI_DATE2." $date ");
	}else {
		$xoopsTpl->assign('no_ad', _ALUMNI_NOCLAS);
    }

    $result8 = $xoopsDB->query("select title from ".$xoopsDB->prefix("alumni_categories")." where cid=".addslashes($cid)."");
    list($ctitle) = $xoopsDB->fetchRow($result8);
	
    $xoopsTpl->assign('link_main', "<a href=\"../$mydirname/\">"._ALUMNI_MAIN."</a>");
	$xoopsTpl->assign('link_cat', "<a href=\"index.php?pa=view&amp;cid=".addslashes($cid)."\">"._ALUMNI_GORUB." $ctitle</a>");
}

/**
 *  function categorynewgraphic
 **/
function categorynewgraphic($cat)
{
    global $xoopsDB, $mydirname;
	
    $newresult = $xoopsDB->query("select date from ".$xoopsDB->prefix("".$mydirname."_listing")." where cid=".mysql_real_escape_string($cat)." and valid = 'Yes' order by date desc limit 1");
    list($timeann)= $xoopsDB->fetchRow($newresult);
	
    $count = 1;
	$startdate = (time()-(86400 * $count));
	if ($startdate < $timeann) {
		return "<img src=\"".XOOPS_URL."/modules/$mydirname/images/newred.gif\" />";
	}
}

######################################################

$pa = !isset($_GET['pa'])? NULL : $_GET['pa'];
$lid = !isset($_GET['lid'])? NULL : $_GET['lid'];
$cid = !isset($_GET['cid'])? NULL : $_GET['cid'];
$usid = isset( $_GET['usid'] ) ? $_GET['usid'] : '' ;
$min = !isset($_GET['min'])? NULL : $_GET['min'];
$show = !isset($_GET['show'])? NULL : $_GET['show'];
$orderby = !isset($_GET['orderby'])? NULL : $_GET['orderby'];

switch($pa)
{
    case "viewlistings":
	$xoopsOption['template_main'] = "".$mydirname."_item.html";
    viewlistings($lid);
    break;

    case "view":
	$xoopsOption['template_main'] = "".$mydirname."_category.html";
    view($cid,$min,$orderby,$show);
    break;
	
    default:
	$xoopsOption['template_main'] = "".$mydirname."_index.html";
    index();
    break;

}

include(XOOPS_ROOT_PATH."/footer.php");
?>