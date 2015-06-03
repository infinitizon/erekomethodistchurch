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
class News extends Functions{
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
	public function __construct($dbo=NULL){
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
	public function _mainMenu() {
		$result = $this->dbo->query("SELECT news_id, news_title, news_detail, date_created 
									FROM news");
		// Create a multidimensional array to conatin a list of items and parents
		$menu = array( 'items' => array(),  'parents' => array() );
		// Builds the array lists with data from the menu table
		while ($items = $result->fetch(PDO::FETCH_ASSOC)){
			// Creates entry into items array with current menu item id ie. $menu['items'][1]
			$menu['items'][$items['page_id']] = $items;
			// Creates entry into parents array. Parents array contains a list of all items with children
			$menu['parents'][$items['parent_id']][] = $items['page_id'];
		}
		return $this->_buildMenu(0, $menu);
	}

	/*
	* Function that generates a dynamic menu for website
	*/
	public function _buildMenu($parent, $menu) {
	   $html = "";
	   if (isset($menu['parents'][$parent])) {
		  $html .= ($parent == 0) ? "<ul class=\"navigation\">\n" : "<ul>\n";
		   foreach ($menu['parents'][$parent] as $itemId) {
			  if(!isset($menu['parents'][$itemId])) {
				 $html .= "<li>\n <a href='".$menu['items'][$itemId]['page_url']."'>".$menu['items'][$itemId]['page_label']."</a>\n</li> \n";
			  }
			  if(isset($menu['parents'][$itemId])) {
				 $html .= "<li>\n <a href='".$menu['items'][$itemId]['page_url']."'>"
				 				.(($parent == 0) ? "<span style=\"float:right;\">&nbsp;&nbsp; &#9661;</span>" : "<span style=\"float:right;\">&#9655;</span>")
								.$menu['items'][$itemId]['page_label']."</a> \n";
				 $html .= $this->_buildMenu($itemId, $menu);
				 $html .= "</li> \n";
			  }
		   }
		   $html .= "</ul> \n";
	   }
	   return $html;
	}
}

?>