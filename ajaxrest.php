<?php

/**
 * DokuWiki AJAX REST SERVICE
 *
 * @author     Josep Cañellas <jcanell4@ioc.cat>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/../../../');
require_once(DOKU_INC.'inc/init.php');
if(!defined('DOKU_COMMANDS')) define('DOKU_COMMANDS',dirname(__FILE__).'/commands/');
if(!defined('REST_COMMAND_PARENT')) define('REST_COMMAND_PARENT','abstract_rest_command_class');
if(!defined('SECTOK_PARAM')) define('SECTOK_PARAM', 0);

//close session
session_write_close();

//if(!isUserAuthenticated()) die("Unauthenticated user");

header('Content-Type: text/html; charset=utf-8');

//global $INPUT;
global $_SERVER;
global $JSINFO;
global $_GET;
global $_POST;

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
  case 'GET':
  case 'HEAD':
    $request_params = $_GET;
    break;
  case 'POST':
    $request_params = $_POST;
    break;
  case 'PUT':
  case 'DELETE':
    parse_str(file_get_contents('php://input'), $request_params);
    break;
}
$extra_url_params = split('/', $_SERVER['PATH_INFO']);
$_REQUEST['sectok']=$extra_url_params[SECTOK_PARAM];


fillinfo();
if(@file_exists(DOKU_INC."lib/plugins/ownInit/init.php")){
    require_once (DOKU_INC."lib/plugins/ownInit/init.php");
    own_init();
}
if(key_exists ($JSINFO['storeDataParamName'], $request_params)){
    $command = $request_params[$JSINFO['storeDataParamName']];
}else if(isset($extra_url_params)){
    $command = $extra_url_params[1];
    $extra_url_params = array_slice($extra_url_params, 2);
}else{
    header('HTTP/1.1 400 Bad Request', true, 400);
    exit;
}

if(existCommand($command)){
    print callCommand($command, $method, $request_params, $extra_url_params);
}else{
    if(!checkSecurityToken()) die("CSRF Attack");

    $dataEvent = array('command' => $call, 'params' => $params);
    $evt = new Doku_Event('AJAX_CALL_UNKNOWN', $dataEvent);
//                            array('command' => $call, 'params' => $params));
    if ($evt->advise_before()) {
        print "AJAX call '".htmlspecialchars($call)."' unknown!\n";
        exit;
    }
    $evt->advise_after();
    unset($evt);
}

function existCommand($command){
    $file = DOKU_COMMANDS.$command.'/'.$command.'_command.php';
    $ret = false;
    if(@file_exists($file)){
        require_once($file);
        $class = new \ReflectionClass($command.'_command');
        if($class->getParentClass()->getName()===REST_COMMAND_PARENT){
            $ret=true;
        }
    }
    return  $ret;
}

function callCommand($str_command, $method, $request_params, $extra_url_params){
    global $INFO;
    $str_command .= '_command';
    $command = new $str_command();
    
    $command->setParameters($request_params);
    $ret = $command->dispatchRequest($method, 
                                        $extra_url_params, 
                                        $INFO['userinfo']['grps']);
    if($command->error){
        $ret=$command->errorMessage;
    }
    return $ret;
}

function fillinfo(){
    global $INFO;
    $INFO = pageinfo();
}

?>
