<?php

//$myfile = fopen("songs/testfile.txt", "w") 
include_once 'core/init.inc.php';
$result = $dbo->query("SELECT page_id, page_label, page_url, parent_id FROM pages WHERE site_position = 1 ORDER BY page_id");
// Create a multidimensional array to conatin a list of items and parents
$menu = array('items' => array(), 'parents' => array());
// Builds the array lists with data from the menu table
while ($items = $result->fetch(PDO::FETCH_ASSOC)) {
    // Creates entry into items array with current menu item id ie. $menu['items'][1]
    $menu['items'][$items['page_id']] = $items;
    // Creates entry into parents array. Parents array contains a list of all items with children
    $menu['parents'][$items['parent_id']][] = $items['page_id'];
}
echo _buildMenu(0, $menu);
function _buildMenu($parent, $menu, $classNm = "main_nav") {
    $html = "";
    if (isset($menu['parents'][$parent])) {
        $html .=  "<select>\n";
        foreach ($menu['parents'][$parent] as $itemId) {
            if (!isset($menu['parents'][$itemId])) {
                $html .= "<option value='" . $menu['items'][$itemId]['page_url'] . "'>" . $menu['items'][$itemId]['page_label'] . "</a>\n</option> \n";
            }
            if (isset($menu['parents'][$itemId])) {
                $html .= "<option label='" . $menu['items'][$itemId]['page_url'] . "'>"
                        . $menu['items'][$itemId]['page_label'] . "</a> \n";
                $html .= _buildMenu($itemId, $menu, $classNm);
                $html .= "</option> \n";
            }
        }
        $html .= "</select> \n";
    }
    return $html;
}
