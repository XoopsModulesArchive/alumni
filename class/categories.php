<?php
// $Id: class.newscategory.php,v 1.9 2004/09/02 17:04:08 hthouzard Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
$main_lang =  '_' . strtoupper( $mydirname ) ;

include_once XOOPS_ROOT_PATH."/modules/$mydirname/class/xoopstree.php";
include_once XOOPS_ROOT_PATH."/modules/$mydirname/include/functions.php";

class AlumniCategory extends XoopsCategory
{
	var $menu;
	var $cat_desc;


	function AlumniCategory($cid=0)
	{
		$this->db =& Database::getInstance();
		$this->table = $this->db->prefix("".$mydirname."_categories");
		if ( is_array($cid) ) {
			$this->makeCategory($cid);
		} elseif ( $cid != 0 ) {
			$this->getCategory(intval($cid));
		} else {
			$this->cid = $cid;
		}
	}

	function MakeMyCategorySelBox($none=0, $selcategory=-1, $selname="", $onchange="", $checkRight = false, $perm_type="".$mydirname."_view")
	{
	    $perms='';
	    if ($checkRight) {
	        global $xoopsUser;
	        $module_handler =& xoops_gethandler('module');
	        $alumniModule =& $module_handler->getByDirname("$mydirname");
	        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
	        $gperm_handler =& xoops_gethandler('groupperm');
	        $categorys = $gperm_handler->getItemIds($perm_type, $groups, $alumniModule->getVar('mid'));
	        if(count($categorys)>0) {
	        	$category = implode(',', $categorys);
	        	$perms = " AND cid IN (".$category.") ";
	        } else {
	        	return null;
	        }
	    }

		if ( $selcategory != -1 ) {
			return $this->makeMySelBox("title", "title", $selcategory, $none, $selname, $onchange, $perms);
		} elseif ( !empty($this->cid) ) {
			return $this->makeMySelBox("title", "title", $this->cid, $none, $selname, $onchange, $perms);
		} else {
			return $this->makeMySelBox("title", "title", 0, $none, $selname, $onchange, $perms);
		}
	}

	/**
 	* makes a nicely ordered selection box
 	*
 	* @param	int	$preset_id is used to specify a preselected item
 	* @param	int	$none set $none to 1 to add a option with value 0
 	*/
	function makeMySelBox($title,$order="",$preset_id=0, $none=0, $sel_name="cid", $onchange="", $perms)
	{
		$myts =& MyTextSanitizer::getInstance();
		$outbuffer='';
		$outbuffer = "<select name='".$sel_name."'";
		if ( $onchange != "" ) {
			$outbuffer .= " onchange='".$onchange."'";
		}
		$outbuffer .= ">\n";
		$sql = "SELECT cid, ".$title." FROM ".$this->table." WHERE (pid=0)".$perms;
		if ( $order != "" ) {
			$sql .= " ORDER BY $order";
		}
		$result = $this->db->query($sql);
		if ( $none ) {
			$outbuffer .= "<option value='0'>----</option>\n";
		}
		while ( list($catid, $name) = $this->db->fetchRow($result) ) {
			$sel = "";
			if ( $catid == $preset_id ) {
				$sel = " selected='selected'";
			}
			$name=$myts->displayTarea($name);
			$outbuffer .= "<option value='$catid'$sel>$name</option>\n";
			$sel = "";
			$arr = $this->getChildTreeArray($catid, $order, $perms);
			foreach ( $arr as $option ) {
				$option['prefix'] = str_replace(".","--",$option['prefix']);
				$catpath = $option['prefix']."&nbsp;".$myts->displayTarea($option[$title]);

				if ( $option['cid'] == $preset_id ) {
					$sel = " selected='selected'";
				}
				$outbuffer .= "<option value='".$option['cid']."'$sel>$catpath</option>\n";
				$sel = "";
			}
		}
		$outbuffer .= "</select>\n";
		return $outbuffer;
	}

	function getChildTreeArray($sel_id=0, $order='', $perms='',$parray = array(), $r_prefix='')
	{
		$sql = "SELECT * FROM ".$this->table." WHERE (pid=".$sel_id.")".$perms;
		if ( $order != "" ) {
			$sql .= " ORDER BY $order";
		}
		$result = $this->db->query($sql);
		$count = $this->db->getRowsNum($result);
		if ( $count == 0 ) {
			return $parray;
		}
		while ( $row = $this->db->fetchArray($result) ) {
			$row['prefix'] = $r_prefix.".";
			array_push($parray, $row);
			$parray = $this->getChildTreeArray($row['cid'],$order,$perms,$parray,$row['prefix']);
		}
		return $parray;
	}

	function getVar($var) {
		if(method_exists($this, $var)) {
			return call_user_func(array($this,$var));
		} else {
	    	return $this->$var;
	    }
	}

	/**
 	* Get the total number of categorys in the base
 	*/
	function getAllCategorysCount($checkRight = true)
	{
	    $perms='';
	    if ($checkRight) {
	        global $xoopsUser;
	        $module_handler =& xoops_gethandler('module');
	        $alumniModule =& $module_handler->getByDirname('alumni');
	        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
	        $gperm_handler =& xoops_gethandler('groupperm');
	        $categorys = $gperm_handler->getItemIds("".$mydirname."_submit", $groups, $alumniModule->getVar('mid'));
	        if(count($categorys)>0) {
	        	$categorys = implode(',', $categorys);
	        	$perms = " WHERE cid IN (".$categorys.") ";
	        } else {
	        	return null;
	        }
	    }

		$sql = "SELECT count(cid) as cpt FROM ".$this->table.$perms;
		$array = $this->db->fetchArray($this->db->query($sql));
		return($array['cpt']);
	}

	function getAllCategorys($checkRight = true, $permission = "".$mydirname."_view")
	{
	    $categorys_arr = array();
	    $db =& Database::getInstance();
	    $table = $db->prefix('categorys');
        $sql = "SELECT * FROM ".$table;
        if ($checkRight) {
			$categorys = alumni_MygetItemIds($permission);
			if (count($categorys) == 0) {
				return array();
			}
			$categorys = implode(',', $categorys);
			$sql .= " WHERE cid IN (".$categorys.")";
		}
		$sql .= " ORDER BY title";
		$result = $db->query($sql);
		while ($array = $db->fetchArray($result)) {
			$category = new AlumniCategory();
			$category->makeCategory($array);
			$categorys_arr[$array['cid']] = $category;
			unset($category);
		}
		return $categorys_arr;
	}


	/**
	* Returns the number of published news per category
	*/
	function getAlumniCountByCategory()
	{
		$ret=array();
		$sql="SELECT count(lid) as cpt, cid FROM ".$this->db->prefix("".$mydirname."_listing")." WHERE (status!=1 AND valid='Yes') GROUP BY cid";
		$result = $this->db->query($sql);
		while ($row = $this->db->fetchArray($result)) {
			$ret[$row["cid"]]=$row["cpt"];
		}
		return $ret;
	}

	/**
	* Returns some stats about a category
	
	function getCategoryMiniStats($cid)
	{
		$ret=array();
		$sql="SELECT count(storyid) as cpt1, sum(counter) as cpt2 FROM ".$this->db->prefix('stories')." WHERE (cid=" . $cid.") AND (published>0 AND published <= ".time().") AND (expired = 0 OR expired > ".time().")";
		$result = $this->db->query($sql);
		$row = $this->db->fetchArray($result);
		$ret['count']=$row["cpt1"];
		$ret['reads']=$row["cpt2"];
		return $ret;
	}
*/

	function setMenu($value)
	{
		$this->menu=$value;
	}
/**
	function setCategory_color($value)
	{
		$this->category_color=$value;
	}
*/
	function getCategory($cid)
	{
		$sql = "SELECT * FROM ".$this->table." WHERE cid=".$cid."";
		$array = $this->db->fetchArray($this->db->query($sql));
		$this->makeCategory($array);
	}

	function makeCategory($array)
	{
		if(is_array($array)) {
			foreach($array as $key=>$value){
				$this->$key = $value;
			}
		}
	}

	function store()
	{
		$myts =& MyTextSanitizer::getInstance();
		$title = "";
		$img = "";
		$cat_desc=$myts->censorString($this->cat_desc);
		$cat_desc= $myts->addSlashes($cat_desc);
//		$category_rssurl=$myts->addSlashes($this->category_rssurl);
//		$category_color=$myts->addSlashes($this->category_color);

		if ( isset($this->title) && $this->title != "" ) {
			$title = $myts->addSlashes($this->title);
		}
		if ( isset($this->img) && $this->img != "" ) {
			$img = $myts->addSlashes($this->img);
		}
		if ( !isset($this->pid) || !is_numeric($this->pid) ) {
			$this->pid = 0;
		}
		$category_frontpage=intval($this->category_frontpage);
		$insert=false;
		if ( empty($this->cid) ) {
			$insert=true;
			$this->cid = $this->db->genId($this->table."_cid_seq");
			$sql = sprintf("INSERT INTO %s (cid, pid, title, cat_desc, img, ordre, affprice) VALUES (%u, %u, '%s', '%s', '%s', '%s', '%s')", $this->table, intval($this->cid), intval($this->pid), $title, $cat_desc, $img, $ordre, $affprice);
		} else {
			$sql = sprintf("UPDATE %s SET pid = %u, title = '%s', cat_desc='%s', img = '%s', ordre='%s', affprice='%s' WHERE cid = %u", $this->table, intval($this->pid), $title, $cat_desc, $img, $ordre, $affprice, intval($this->cid));
		}
		if ( !$result = $this->db->query($sql) ) {
			// TODO: Replace with something else
			ErrorHandler::show('0022');
		} else {
			if($insert) {
				$this->cid = $this->db->getInsertId();
			}
		}

		if ( $this->use_permission == true ) {
			$xt = new XoopsTree($this->table, "cid", "pid");
			$parent_categorys = $xt->getAllParentId($this->cid);
			if ( !empty($this->m_groups) && is_array($this->m_groups) ){
				foreach ( $this->m_groups as $m_g ) {
					$moderate_categorys = XoopsPerms::getPermitted($this->mid, "ModInCategory", $m_g);
					$add = true;
					// only grant this permission when the group has this permission in all parent categorys of the created category
					foreach($parent_categorys as $p_category){
						if ( !in_array($p_category, $moderate_categorys) ) {
							$add = false;
							continue;
						}
					}
					if ( $add == true ) {
						$xp = new XoopsPerms();
						$xp->setModuleId($this->mid);
						$xp->setName("ModInCategory");
						$xp->setItemId($this->cid);
						$xp->store();
						$xp->addGroup($m_g);
					}
				}
			}
			if ( !empty($this->s_groups) && is_array($this->s_groups) ){
				foreach ($this->s_groups as $s_g ) {
					$submit_categorys = XoopsPerms::getPermitted($this->mid, "SubmitInCategory", $s_g);
					$add = true;
					foreach($parent_categorys as $p_category){
						if ( !in_array($p_category, $submit_categorys) ) {
							$add = false;
							continue;
						}
					}
					if ( $add == true ) {
						$xp = new XoopsPerms();
						$xp->setModuleId($this->mid);
						$xp->setName("SubmitInCategory");
						$xp->setItemId($this->cid);
						$xp->store();
						$xp->addGroup($s_g);
					}
				}
			}
			if ( !empty($this->r_groups) && is_array($this->r_groups) ){
				foreach ( $this->s_groups as $r_g ) {
					$read_categorys = XoopsPerms::getPermitted($this->mid, "ReadInCategory", $r_g);
					$add = true;
					foreach($parent_categorys as $p_category){
						if ( !in_array($p_category, $read_categorys) ) {
							$add = false;
							continue;
						}
					}
					if ( $add == true ) {
						$xp = new XoopsPerms();
						$xp->setModuleId($this->mid);
						$xp->setName("ReadInCategory");
						$xp->setItemId($this->cid);
						$xp->store();
						$xp->addGroup($r_g);
					}
				}
			}
		}
		return true;
	}

	function menu()
	{
		return $this->menu;
	}

	function cat_desc($format="S")
	{
		$myts =& MyTextSanitizer::getInstance();
		switch($format){
			case "S":
				$cat_desc = $myts->displayTarea($this->cat_desc,1);
				break;
			case "P":
				$cat_desc = $myts->previewTarea($this->cat_desc);
				break;
			case "F":
			case "E":
				$cat_desc = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->cat_desc));
				break;
		}
		return $cat_desc;
	}

	function img($format="S")
	{
		if(trim($this->img)=='') {
			$this->img='blank.png';
		}
		$myts =& MyTextSanitizer::getInstance();
		switch($format){
			case "S":
				$img= $myts->htmlSpecialChars($this->img);
				break;
			case "E":
				$img = $myts->htmlSpecialChars($this->img);
				break;
			case "P":
				$img = $myts->htmlSpecialChars($this->img);
				break;
			case "F":
				$img = $myts->htmlSpecialChars($this->img);
				break;
		}
		return $img;
	}


	function getCategoryTitleFromId($category,&$categorystitles)
	{
		$myts =& MyTextSanitizer::getInstance();
		$sql="SELECT cid, title, img FROM ".$this->table." WHERE ";
	    if (!is_array($category)) {
        	$sql .= " cid=".intval($category);
	    } else {
	    	if(count($category)>0) {
	        	$sql .= " cid IN (".implode(',', $category).")";
	    	} else {
	    		return null;
	    	}
	    }
	    $result = $this->db->query($sql);
		while ($row = $this->db->fetchArray($result)) {
			$categorystitles[$row["cid"]]=array("title"=>$myts->displayTarea($row["title"]),"picture"=>XOOPS_URL."/modules/$mydirname/images/cat/".$row["img"]);
		}
		return $categorystitles;
	}


	function &getCategoryList($perms=true)
	{
		$sql='SELECT cid, pid, title FROM '.$this->table." WHERE 1 ";
		
//		if($perms) {
			$categoryids=array();
			$categoryids=alumni_MygetItemIds("".$mydirname."_view");
            if (count($categoryids) == 0) {
            	return '';
            }
            $category = implode(',', $categoryids);
            $sql .= " AND cid IN (".$category.")";
//		}
		$result = $this->db->query($sql);
		$ret = array();
		$myts =& MyTextSanitizer::getInstance();
		while ($myrow = $this->db->fetchArray($result)) {
			$ret[$myrow['cid']] = array('title' => $myts->displayTarea($myrow['title']), 'pid' => $myrow['pid']);
		}
		return $ret;
	}

	function setCategoryDescription($value)
	{
		$this->cat_desc = $value;
	}
}
?>