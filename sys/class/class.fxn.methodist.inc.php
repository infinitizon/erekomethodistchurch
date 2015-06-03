<?php

/**
 * General functions class
 *
 * PHP version 5+
 *
 * LICENSE: This source file is subject to the MIT License, available at http://www.opensource.org/licenses/mit-license.html
 *
 * @author Abimbola Hassan <ahassan@infinitizon.com>
 * @copyright 2014 infinitizon Design
 * @license http://www.opensource.org/licenses/mit-license.html
 */
class Functions extends DB_Connect {

    /**
     * The eventual student as an array
     * @the_fxn string Stores the student as an array
     */
    private $the_fxn = '';

    /**
     * Creates a database object and stores relevant data
     *
     * Upon instantiation, this class accepts a database object that, if not null, is stored in the object's private $_db
     * property. If null, a new PDO object is created and stored instead.
     *
     * @param object $dbo a database object
     * @return void
     */
    public function __construct($dbo = NULL) {
        /*
         * Call the parent constructor to check for a database object
         */
        parent::__construct($dbo);
    }

    /**
     * Explode a text into an array based on delimeters (array) specified as part of the string
     *
     * @return array
     */
    public function _multiexplode($string, $delimiters = array("-", " ")) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

    /**
     * Method  : _subStrAtDel - Substring at delimeter - Get a part of a string based on a delimiter
     * @param  : $string     - String to be worked on.
     * 			$del        - Delimeter to use
     *           $fromRear   - Boolean that tells if we should start from behind or from begining.
     * @return : String
     */
    public function _subStrAtDel($string, $del, $fromRear = true) {
        return ($fromRear) ? substr($string, 0, strrpos($string, $del) + 1) : substr($string, strpos($string, $del) + 1);
    }

    /**
     * Returns the various combinations of items in an array
     * Example: array('a', 'b') should return 'a b' and 'b a'
     *
     * @return String
     */
    public function _strCombs($items, $perms = array(), $col_nm) {
        if (empty($items)) {
            $this->the_fxn .= " '%" . join('%', $perms) . "%' OR $col_nm LIKE";
        } else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $this->_strCombs($newitems, $newperms, $col_nm);
            }
        }
        return $this->the_fxn;
    }

    /**
     * Get LOV Description
     *
     * @return void
     */
    public function _getLOVDsc($get_LOVDsc, $val_dsc) {
        try {
            $stmt = $this->dbo->query($get_LOVDsc);
            while ($items = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->the_fxn = $items[$val_dsc];
            }
            return $this->the_fxn;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Function that helps us build a dropdown menu. Works for either a simple select or a combobox
     * Note: We may need to style the combobox to give it the needed feel.
     * @param:	$query		- Pass in the query because we do not know how the limit clause will look
     * 			$val_id		- Id that is returned from the select
     * 			$val_dsc	- Value shown in the dropdown box
     * 			$select_nm	- Name of the select list
     * 		 	$class_nm	- Classname you want to pass to the select. Use combobox if you want a combobox
     * 		 	$null_val	- If you want a null option at the begining. Default: NULL
     * 		 	$curr_val	- The currently selected value
     * @return:	String		- The built select/combo box
     */

    public function _getLOVs($query, $val_id, $val_dsc, $select_nm, $class_nm, $null_val = NULL, $curr_val = NULL) {
        try {
            $stmt = $this->dbo->query($query);
            $this->the_fxn = "<select class='" . $class_nm;
            $this->the_fxn .= (strpos($class_nm, 'combobox') !== false) ? "" : "' name='" . $select_nm;
            $this->the_fxn .= "'>";  // Close select tag
            $this->the_fxn .=($null_val) ? "<option value=''>$null_val</option>" : '';
            while ($items = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($items);
                $this->the_fxn .= "<option value='" . $val_id . "'";
                $this->the_fxn .=($curr_val != NULL && $curr_val == $val_id) ? ' selected="selected" ' : '';
                $this->the_fxn .=">" . $val_dsc . "</option>";
            }
            $this->the_fxn .= "</select>";
            $this->the_fxn .= (strpos($class_nm, 'combobox') !== false) ? '<input class="combo_in" name="' . $select_nm . '" value="' . $curr_val . '" />' : "";
            //return $this->the_fxn;
            return $this->the_fxn;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function _getStateOptLOV($query, $val_id, $val_dsc, $null_val = NULL, $curr_val = NULL) {
        try {
            $stmt = $this->dbo->query($query);
            $this->the_fxn .=($null_val) ? "<option value=''>$null_val</option>" : '';
            while ($items = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($items);
                $this->the_fxn .= "<option value='" . $$val_id . "'";
                $this->the_fxn .=($curr_val != NULL && $curr_val == $$val_id) ? ' selected="selected" ' : '';
                $this->the_fxn .=">" . $$val_dsc . "</option>";
            }
            //return $this->the_fxn;
            return $this->the_fxn;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Function that performs database query
     * @param:	$query		- query to execute
     * 		$params		- parameters to use in the query execution
     * 		$multiple	- boolean: whether it returns a multiple set of a single set
     * @return:	Array		- The built result set. 
     */

    public function _execQuery($query, $isSelect = true, $isMultiple = true, $params = array()) {
        try {
            $stmt = $this->dbo->prepare($query);
            if ($stmt->execute($params)) {
                if ($isSelect) {
                    if ($isMultiple) {
                        $results = array();
                        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $results[] = $result;
                        }
                        return $results;
                    } else {
                        $results = $stmt->fetch(PDO::FETCH_ASSOC);
                        return $results;
                    }
                } else {
                    return $stmt->rowCount();
                }
            } else {
                return "Error parsing query. Please check your query";
            }
        } catch (Exception $e) {
            return array('result' => 'Failure', 'msg' => "The Transaction batch failed due to the following reason:<blockquote>" . $e->getMessage() . "</blockquote>");
        }
    }
    public function _getResultHeaders($query) {
        $rs = $this->dbo->query($query);
        for ($i = 0; $i < $rs->columnCount(); $i++) {
            $col = $rs->getColumnMeta($i);
            $columns[] = $col['name'];
        }
        return $columns;
    }
    /**
     * Build a table from an array (array of array) of table data
     * @param:	$array		- (array of array): $array[0] holds the assoc of db cols and title
     * 			$isVertical	- Boolean: determine whether the column headers should be on the left 
     * 			$classNm	- Classname you want to give the table
     * @return:	String		- The built table set. 
     */

    public function _buildTable($array, $isVertical = true, $tblTitle = array(), $classNm = "vertColNm") {
        $table = '<table  class="my_tables ' . $classNm . '">';
        if (!empty($tblTitle)) {
            $table .= "<tr>";
            foreach ($array[0] as $key => $values) {
                $table .= "<th>" . $tblTitle[$key] . "</th>";
            }
            $table .= "</tr>";
        }
        if ($isVertical) {
            foreach ($array[0] as $key => $values) {
                $table .= "<tr>";
                for ($i = 0; $i < count($array); $i++) {
                    $table .= ($i == 0 && empty($tblTitle)) ? "<th>" . $array[$i][$key] . "</th>" : "<td>" . $array[$i][$key] . "</td>";
                }
                $table .= "</tr>";
            }
        } else {
            for ($i = 0; $i < count($array); $i++) {
                $table .= "<tr>";
                foreach ($array[0] as $key => $values) {
                    $table .= ($i == 0 && empty($tblTitle)) ? "<th>" . $array[$i][$key] . "</th>" : "<td>" . $array[$i][$key] . "</td>";
                }
                $table .= "</tr>";
            }
        }
        $table .= '</table>';
        return $table;
    }

    public function _readMore($string, $length2cut, $readMoreLink) {
        if (strlen($string) > $length2cut) {
            // truncate string
            $stringCut = substr($string, 0, $length2cut); // truncate string
            // make sure it ends in a word so beautiful doesn't become beu...
            return substr($stringCut, 0, strrpos($stringCut, ' ')) . '...' .(isset($readMoreLink)? $readMoreLink . 'Read More</a>':'');
        }
    }

    /**
     * Execute request and gets result over disparate networks
     * @param:	$URL		- URL to connect to
     * 			$headers	- headers to pass in connection query
     * 			$data		- data to consume
     * @return:	String		- $output - returned service to consume. 
     */

    public function _consumeService($URL, $data, $headers = array()) {
        $ch = curl_init($URL);
        //curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
    * Checks if a value is null or not
    * @return:	Boolean
    */

    public function _isnull($data) {
        /** only if you need this
          if (is_string($data)) {
          $data = strtolower($data);
          }
         */
        switch ($data) {
            // Add whatever your definition of null is
            // This is just an example
            //-----------------------------
            case 'unknown': // continue
            case 'undefined': // continue
            //-----------------------------
            case 'null': // continue
            case 'NULL': // continue
            case NULL:
                return true;
        }
        // return false by default
        return false;
    }

    public function _formatMoney($number, $fractional = false) {
        if ($fractional) {
            $number = sprintf('%.2f', $number);
        }
        while (true) {
            $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
            if ($replaced != $number) {
                $number = $replaced;
            } else {
                break;
            }
        }
        return $number;
    }

    public function _mainMenu($parent = 0, $classNm = "main_nav") {
        $result = $this->dbo->query("SELECT page_id, page_label, page_url, parent_id 
									FROM pages
									WHERE site_position = 1
									ORDER BY page_id, sort_order, page_label");
        // Create a multidimensional array to conatin a list of items and parents
        $menu = array('items' => array(), 'parents' => array());
        // Builds the array lists with data from the menu table
        while ($items = $result->fetch(PDO::FETCH_ASSOC)) {
            // Creates entry into items array with current menu item id ie. $menu['items'][1]
            $menu['items'][$items['page_id']] = $items;
            // Creates entry into parents array. Parents array contains a list of all items with children
            $menu['parents'][$items['parent_id']][] = $items['page_id'];
        }
        return $this->_buildMenu($parent, $menu, $classNm);
    }

    /**
     * Function that generates a dynamic menu for website
     */

    public function _buildMenu($parent, $menu, $classNm = "main_nav") {
        $html = "";
        if (isset($menu['parents'][$parent])) {
            $html .= ($parent == 0 && $classNm == "main_nav") ? "<ul class=\"$classNm\">\n" : "<ul>\n";
            foreach ($menu['parents'][$parent] as $itemId) {
                if (!isset($menu['parents'][$itemId])) {
                    $html .= "<li>\n <a href='" . $menu['items'][$itemId]['page_url'] . "'>" . $menu['items'][$itemId]['page_label'] . "</a>\n</li> \n";
                }
                if (isset($menu['parents'][$itemId])) {
                    $html .= "<li>\n <a href='" . $menu['items'][$itemId]['page_url'] . "'>"
                            . (($parent == 0) ? "<span style=\"float:right;\">&nbsp;&nbsp; &#9661;</span>" : "<span style=\"float:right;\">&#9655;</span>")
                            . $menu['items'][$itemId]['page_label'] . "</a> \n";
                    $html .= $this->_buildMenu($itemId, $menu, $classNm);
                    $html .= "</li> \n";
                }
            }
            $html .= "</ul> \n";
        }
        return $html;
    }
    /*
     * Function that builds query for a search parameter paging for a query
    */

    public function _buildSearchQry($sqlSearchCount, $sqlSearchQuery, $search_term, $columns) {
        $term = NULL;
        if (count($search_term) > 1) {
            $term = $search_term[1];
            if ($search_term[0] == 'date') {
                $search_term[1] = date('Y-m-d', strtotime($search_term[1]));
            }
            $splitVal = $this->_multiexplode($search_term[1], array(" "));
            switch (strtolower($search_term[0])) {
                case 'scripture':
                    $passage = $this->_strCombs($splitVal, array(), 'passage');
                    $passage = $this->_subStrAtDel($passage, "'", true);
                    $passage = ' passage LIKE ' . $passage . " ";
                    $text = $this->_strCombs($splitVal, array(), 'text');
                    $text = $this->_subStrAtDel($text, "'", true);
                    $text = ' text LIKE ' . $text . " ";
                    $search_term = $text;
                    break;
                default:
                    $any = $this->_strCombs($splitVal, array(), $search_term[0]);
                    $any = $this->_subStrAtDel($any, "'", true);
                    $search_term = " {$search_term[0]} LIKE " . $any . " ";
                    break;
            }
            $sqlSearchCount = $sqlSearchCount . $search_term;
            $sqlSearchQuery = $sqlSearchQuery . $search_term;
        } else {
            $term = $search_term[0];
            $search_term = $this->_multiexplode($search_term[0], array(" "));
            foreach ($columns as $column) {
                $$column = $this->_strCombs($search_term, array(), $column);
                $result = $this->_subStrAtDel($$column, "'", true);
                $result = " $column LIKE $result OR ";
            }
            return array('sqlSearchCount' => $this->_subStrAtDel($sqlSearchCount . $result, "'", true)
                , 'sqlSearchQuery' => $this->_subStrAtDel($sqlSearchQuery . $result, "'", true)
            );
        }
    }

    /*
     * Function that prepares paging for a query
     */

    public function _preparePaging($numrows, $rowsperpage = 10, $currentpage = 1) {
        // find out total pages
        $totalpages = ceil($numrows / $rowsperpage);
        // get the current page or set a default
        if (isset($currentpage) && is_numeric($currentpage)) {
            $currentpage = (int) $currentpage; // cast var as int incase a funny person tries something phony
        } else {
            $currentpage = 1; // default page num
        } // end if
        // if current page is greater than total pages...
        if ($currentpage > $totalpages) {
            $currentpage = $totalpages; // set current page to last page
        } // end if
        // if current page is less than first page...
        if ($currentpage < 1) {
            $currentpage = 1; // set current page to first page
        } // end if
        // the offset of the list, based on current page 
        $offset = ($currentpage - 1) * $rowsperpage;

        return array('currentpage' => $currentpage
            , 'totalpages' => $totalpages
            , 'offset' => $offset);
    }

    /*
     * Function that build the pagination links
     */

    public function _buildPagingLinks($range = 3, $currentpage, $totalpages, $divLinkClass = array(), $link = WEB_ROOT) {
        $linkClass = array('div' => '', 'link' => '', 'param'=>'');
        $divLinkClass = array_merge($linkClass,$divLinkClass);
        $div = "<div class='" . @$divLinkClass['div'] . "'>";
        // if not on page 1, don't show back links
        if ($currentpage > 1) {
            // show << link to go back to page 1
            $div .= " <a href='{$link}?currentpage=1&{$divLinkClass['param']}' class='" . @$divLinkClass['link'] . "' currentpage=1><< First</a> ";
            // get previous page num
            $prevpage = $currentpage - 1;
            // show < link to go back to 1 page
            $div .= " <a href='{$link}?currentpage=$prevpage&{$divLinkClass['param']}' class='" . @$divLinkClass['link'] . "' currentpage=$prevpage><</a> ";
        } // end if 
        // loop to show links to range of pages around current page
        for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
            // if it's a valid page number...
            if (($x > 0) && ($x <= $totalpages)) {
                // if we're on current page...
                if ($x == $currentpage) {
                    $div .= " [<b>$x of $totalpages</b>] "; // 'highlight' it but don't make a link
                } else {
                    $div .= " <a href='{$link}?currentpage=$x&{$divLinkClass['param']}' class='" . @$divLinkClass['link'] . "' currentpage=$x>$x</a> "; // make it a link
                } // end else
            } // end if 
        } // end for
        // if not on last page, show forward and last page links        
        if ($currentpage != $totalpages) {
            $nextpage = $currentpage + 1;
            // echo forward link for next page 
            $div .= " <a href='{$link}?currentpage=$nextpage&{$divLinkClass['param']}' class='" . @$divLinkClass['link'] . "' currentpage=$nextpage>></a> ";
            // echo forward link for lastpage
            $div .= " <a href='{$link}?currentpage=$totalpages&{$divLinkClass['param']}' class='" . @$divLinkClass['link'] . "' currentpage=$totalpages>Last >></a> ";
        } // end if
        $div .= "</div>";
        return $div;
    }

}

?>