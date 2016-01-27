<?php
/**
 * DokuWiki AJAX REST SERVICE
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

if(!defined('DOKU_INC')) define('DOKU_INC', dirname(__FILE__) . '/../../../');
require_once(DOKU_INC . 'inc/init.php');
require_once(dirname(__FILE__) . '/ajaxClasses.php');

$inst = ajaxRest::Instance();
$inst->requestHtmlParams();
if ($inst->setCommand()) {
    $inst->loadOwn();
    $inst->process();
}
