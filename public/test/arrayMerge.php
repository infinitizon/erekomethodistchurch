<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$linkClass = array('div' => '', 'link' => '', 'param'=>'');
$param = "getPage=1&link=/news-and-events";
$newClass = array( 'link' => 'news','param'=>$param);
$result = array_merge($linkClass,$newClass);
var_dump($result);