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

	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

	require_once( XOOPS_ROOT_PATH."/modules/$mydirname/include/gtickets.php" ) ;

function copyright()
{
	global $xoopsTpl, $mydirname;
	include(XOOPS_ROOT_PATH."/modules/$mydirname/xoops_version.php");
	$cr_developed = "$mydirname ".$modversion['version']." "._ALUMNI_FOR." Xoops "._ALUMNI_CREATBY." <a href=\"http://www.jlmzone.com/\" target=\"_blank\">John Mordo</a>";
	$cr_redesigned = "redesigned from myads 2.04";

	if (isset($GLOBALS['xoopsOption']['template_main'])) {
		$xoopsTpl->assign('cr_developed', $cr_developed);
		$xoopsTpl->assign('cr_redesigned', $cr_redesigned);
	}
}

function getTotalItems($sel_id, $status=""){
	global $xoopsDB, $mytree, $mydirname;
	$categories = alumni_MygetItemIds("".$mydirname."_view");
	$count = 0;
	$arr = array();
	if(in_array($sel_id, $categories)) {
		$query = "select count(*) from ".$xoopsDB->prefix("".$mydirname."_listing")." where cid=".intval($sel_id)." and valid='Yes'";
		$result = $xoopsDB->query($query);
		list($thing) = $xoopsDB->fetchRow($result);
		$count = $thing;
		$arr = $mytree->getAllChildId($sel_id);
		$size = count($arr);
		for($i=0;$i<$size;$i++){
			if(in_array($arr[$i], $categories)) {
				$query2 = "select count(*) from ".$xoopsDB->prefix("".$mydirname."_listing")." where cid=".intval($arr[$i])." and valid='Yes'";
				
				$result2 = $xoopsDB->query($query2);
				list($thing) = $xoopsDB->fetchRow($result2);
				$count += $thing;
			}
		}
	}
	return $count;
}

function alumni_MygetItemIds($permtype)
{
	global $xoopsUser, $mydirname;
	static $permissions = array();
	if(is_array($permissions) && array_key_exists($permtype, $permissions)) {
		return $permissions[$permtype];
	}

   	$module_handler =& xoops_gethandler('module');
   	$myalumniModule =& $module_handler->getByDirname("$mydirname");
   	$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
   	$gperm_handler =& xoops_gethandler('groupperm');
   	$categories = $gperm_handler->getItemIds($permtype, $groups, $myalumniModule->getVar('mid'));
   	$permissions[$permtype] = $categories;
    return $categories;
}

function ShowImg()
{	
	global $mydirname;
	
	echo "<script type=\"text/javascript\">\n";
	echo "<!--\n\n";
	echo "function showimage() {\n";
	echo "if (!document.images)\n";
	echo "return\n";
	echo "document.images.avatar.src=\n";
	echo "'".XOOPS_URL."/modules/$mydirname/images/cat/' + document.imcat.img.options[document.imcat.img.selectedIndex].value\n";
	echo "}\n\n";
	echo "//-->\n";
	echo "</script>\n";
}

function ShowImg2()
{
	global $mydirname;
	
	echo "<script type=\"text/javascript\">\n";
	echo "<!--\n\n";
	echo "function showimage2() {\n";
	echo "if (!document.images)\n";
	echo "return\n";
	echo "document.images.scphoto.src=\n";
	echo "'".XOOPS_URL."/modules/$mydirname/images/schools/' + document.imcat.scphoto.options[document.imcat.scphoto.selectedIndex].value\n";
	echo "}\n\n";
	echo "//-->\n";
	echo "</script>\n";
}
//Reusable Link Sorting Functions
function alumni_convertorderbyin($orderby) {
	switch (trim($orderby)) {
	case "nameA":
		$orderby = "name ASC";
		break;
	case "dateA":
		$orderby = "date ASC";
		break;
	case "hitsA":
		$orderby = "hits ASC";
		break;
	case "nameD":
		$orderby = "name DESC";
		break;
	case "hitsD":
		$orderby = "hits DESC";
		break;
	case"dateD":
	default:
		$orderby = "date DESC";
		break;
	}
	return $orderby;
}

function alumni_convertorderbytrans($orderby) {

	global $main_lang;

            if ($orderby == "hits ASC")   $orderbyTrans = "".constant($main_lang."_POPULARITYLTOM")."";
            if ($orderby == "hits DESC")    $orderbyTrans = "".constant($main_lang."_POPULARITYMTOL")."";
            if ($orderby == "name ASC")    $orderbyTrans = "".constant($main_lang."_NAMEATOZ")."";
           if ($orderby == "name DESC")   $orderbyTrans = "".constant($main_lang."_NAMEZTOA")."";
            if ($orderby == "date ASC") $orderbyTrans = "".constant($main_lang."_DATEOLD")."";
            if ($orderby == "date DESC")   $orderbyTrans = "".constant($main_lang."_DATENEW")."";
            return $orderbyTrans;
}
function alumni_convertorderbyout($orderby) {
            if ($orderby == "name ASC")            $orderby = "nameA";
            if ($orderby == "date ASC")            $orderby = "dateA";
            if ($orderby == "hits ASC")          $orderby = "hitsA";
            if ($orderby == "name DESC")              $orderby = "nameD";
            if ($orderby == "date DESC")            $orderby = "dateD";
            if ($orderby == "hits DESC")          $orderby = "hitsD";
            return $orderby;
}

function alumni_getEditor($caption, $name, $value = '', $width = '100%', $height = '300px', $supplemental=''){

    global $xoopsModuleConfig;


$editor = false;
$x22=false;
$xv=str_replace('XOOPS ','',XOOPS_VERSION);
if(substr($xv,2,1)=='2') {
$x22=true;
}
$editor_configs=array();
$editor_configs["name"] =$name;
$editor_configs["value"] = $value;
$editor_configs["rows"] = 25;
$editor_configs["cols"] = 80;
$editor_configs["width"] = "100%";
$editor_configs["height"] = "300px";

    switch(strtolower($xoopsModuleConfig['alumni_form_options'])){

        case 'tinymce' :
        if (!$x22) {

            if ( is_readable(XOOPS_ROOT_PATH . "/class/xoopseditor/tinymce/formtinymce.php"))    {
                include_once(XOOPS_ROOT_PATH . "/class/xoopseditor/tinymce/formtinymce.php");
                $editor = new XoopsFormTinymce(array('caption'=>$caption, 'name'=>$name, 'value'=>$value, 'width'=>'100%', 'height'=>'400px'));
            } else {
                if ($dhtml) {
                    $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 20, 60);
                } else {
                    $editor = new XoopsFormTextArea($caption, $name, $value, 7, 60);
                }
            }
        } else {
            $editor = new XoopsFormEditor($caption, "tinyeditor", $editor_configs);
        }
        break;

        case 'fckeditor' :
        if (!$x22) {
            if ( is_readable(XOOPS_ROOT_PATH . "/class/xoopseditor/fckeditor/formfckeditor.php"))    {
                include_once(XOOPS_ROOT_PATH . "/class/xoopseditor/fckeditor/formfckeditor.php");
                $editor = new XoopsFormFckeditor($editor_configs,true);
            } else {
                if ($dhtml) {
                    $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 20, 60);
                } else {
                    $editor = new XoopsFormTextArea($caption, $name, $value, 7, 60);
                }
            }
        } else {
            $editor = new XoopsFormEditor($caption, "fckeditor", $editor_configs);
        }
        break;

        case 'koivi' :

            if ( is_readable(XOOPS_ROOT_PATH . "/class/wysiwyg/formwysiwygtextarea.php"))    {
                include_once(XOOPS_ROOT_PATH . "/class/wysiwyg/formwysiwygtextarea.php");
//		include_once(XOOPS_ROOT_PATH . "/class/xoopseditor/koivi/language/english.php");
                $editor = new XoopsFormWysiwygTextArea($caption, $name, $value, '100%', '400px', 'small');
            } else {
                if ($dhtml) {
                    $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 20, 60);
                } else {
                    $editor = new XoopsFormTextArea($caption, $name, $value, 7, 60);
                }
            }
        
        break;

        case "textarea":
        if(!$x22) {
            if ( is_readable(XOOPS_ROOT_PATH . "/class/xoopseditor/textarea/textarea.php"))    {
                include_once(XOOPS_ROOT_PATH . "/class/xoopseditor/textarea/textarea.php");
                $editor = new FormTextArea($caption, $name, $value);
            } else {
                if ($dhtml) {
                    $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 20, 60);
                } else {
                    $editor = new XoopsFormTextArea($caption, $name, $value, 7, 60);
                }
            }
        } else {
            $editor = new XoopsFormEditor($caption, "htmlarea", $editor_configs);
        }
        break;

        default :
        if ($dhtml) {
		include_once(XOOPS_ROOT_PATH . "/class/xoopseditor/dhtmltextarea/dhtmltextarea.php");
            $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, 20, 40, $supplemental);
        } else {
            $editor = new XoopsFormEditor($caption, 'dhtmltextarea', $editor_configs);
        }

        break;
    }




    return $editor;
}
?>