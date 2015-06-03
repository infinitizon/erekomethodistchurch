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
class Auth extends DB_Connect{
	private $the_customer;
	private $fxns;
	
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
		$this->fxns = new Functions($dbo);
	}
	/**
	* Function to authenticate a user
	*
	* @return array: User authentication details
	*/
	public function _authenticate ($string) {
		$xml_data = file_get_contents('http://'.$_SERVER['HTTP_HOST']."/cleanup/assets/common/login.xml");
		$URL = "http://192.168.2.14:8082/jbi";
		$headers = array('Content-Type: application/xml',
						 'Pragma: no-cache',
						 'Cache Control: no-cache',
						 'Authorization: Basic '.$string
						 );
		$output =  $this->fxns->_consumeService($URL, $xml_data, $headers);
		//return $output;
		$query_response = simplexml_load_string($output);
		if(@$query_response->ENTITY){
			foreach($query_response->ENTITY as $entity){
				if($entity['entityspec'] == 'AuthRole'){
					foreach($entity->PROPERTY as $key){
						if($key['path'] == 'name') $user['authrole'][] = (String)$key['value'];
					}
				}
				if($entity['entityspec'] == 'LocalePreference'){
					foreach($entity->PROPERTY as $key){
						if($key['path'] == 'locality.code') $user['locale'][] = (String)$key['value'];
					}
				}
				if($entity['entityspec'] == 'UserAuthInfo'){
					foreach($entity->PROPERTY as $key){
						if($key['path'] == 'password') $user['user_hash'][] = (String)$key['value'];
					}
				}
			}
			return $user;
		}elseif(@$query_response->ERRORS){
			return $query_response->ERRORS->ERROR['message'];
		}else{ // Didnt get a response
			return "Server may be unavailable";
		}
	}
}

?>