<?php
//  -----------------------------------------------------------------------  //
//                           Alumni for Xoops 2.0x                             //
//                  By John Mordo from the myAds 2.04 Module                 //
//                    All Original credits left below this                   //
//                                                                           //
//                                                                           //
//                                                                           //
// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Kazumi Ono
// Author Website : http://www.mywebaddons.com/ , http://www.myweb.ne.jp
// Licence Type   : GPL
// ------------------------------------------------------------------------- //

class PageNav{

	var $total;
	var $perpage;
	var $current;
	var $url;

	function PageNav($total_items, $items_perpage, $current_start, $start_name="start", $extra_arg=""){
		$this->total = intval($total_items);
		$this->perpage = intval($items_perpage);
		$this->current = intval($current_start);
		if ( $extra_arg != '' && ( substr($extra_arg, -5) != '&amp;' || substr($extra_arg, -1) != '&' ) ) {
			$extra_arg .= '&amp;';
		}
		$this->url = $_SERVER['PHP_SELF'].'?'.$extra_arg.trim($start_name).'=';
	}

	function renderNav($offset = 4){
		if ( $this->total < $this->perpage ) {
			return;
		}

      $total_pages = ceil($this->total / $this->perpage);
		if ( $total_pages > 1 ) {
   			$ret = '';
			$prev = $this->current - $this->perpage;
				$ret .= '<table width=100% border=0><tr><td height=1 BGCOLOR="#000000" colspan=3></td></tr><tr>';
			if ( $prev >= 0 ) {
				$ret .= '<td align="left"><a href="'.$this->url.$prev.'">&laquo;&laquo; '._ALUMNI_PREV.'</a></td>';
			} else {
				$ret .= '<td align="left"><font color="#C0C0C0">&laquo;&laquo; '._ALUMNI_PREV.'</font></td>';
			}
				$ret .= '<td align="center">';
			$counter = 1;
			$current_page = intval(floor(($this->current + $this->perpage) / $this->perpage));
			while ( $counter <= $total_pages ) {
				if ( $counter == $current_page ) {
					$ret .= '<b>'.$counter.'</b> ';
				} elseif ( ($counter > $current_page-$offset && $counter < $current_page + $offset ) || $counter == 1 || $counter == $total_pages ) {
					if ( $counter == $total_pages && $current_page < $total_pages - $offset ) {
						$ret .= '... ';
					}
					$ret .= '<a href="'.$this->url.(($counter - 1) * $this->perpage).'">'.$counter.'</a> ';
					if ( $counter == 1 && $current_page > 1 + $offset ) {
						$ret .= '... ';
					}
				}
				$counter++;
			}
				$ret .= '</td>';   
			
			$next = $this->current + $this->perpage;
			if ( $this->total > $next ) {
				$ret .= '<td align="right"><a href="'.$this->url.$next.'">'._ALUMNI_NEXT.' &raquo;&raquo;</a></td>';
			} else {
				$ret .= '<td align="right"><font color="#C0C0C0">'._ALUMNI_NEXT.' &raquo;&raquo;</font></td>';
			}
			$ret .= '</tr><tr><td height=1 bgcolor="#000000" colspan=3></td></tr></table>';
		}
		return $ret;
	}

}

?>