<?php
/**
 * DokuWiki AJAX CALL SERVICE
 * Executa un command a partir de les dades rebudes a les variables $_POST o $_GET.
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

if(!defined('DOKU_INC')) define('DOKU_INC', dirname(__FILE__) . '/../../../');
require_once(DOKU_INC . 'inc/init.php');
require_once(DOKU_INC . 'inc/template.php');
require_once(dirname(__FILE__) . '/ajaxClasses.php');

$inst = ajaxCall::Instance();
$without = $inst->setCommand();
if ($without !== NULL) {
    $inst->setRequestParams( $inst->getParams($without) );
    $inst->loadOwn();
    $inst->process();
}
