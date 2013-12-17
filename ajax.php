<?php
/**
 * DokuWiki AJAX CALL SERVICE
 *
 * @author     Josep Cañellas <jcanell4@ioc.cat>
 */


if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/../../../');
require_once(DOKU_INC.'inc/init.php');
if(!defined('DOKU_COMMANDS')) define('DOKU_COMMANDS',dirname(__FILE__).'/commands/');
//close session
session_write_close();


header('Content-Type: text/html; charset=utf-8');

//global $INPUT;
global $_SERVER;
//global $JSINFO;
global $_GET;
global $_POST;

//call the requested function
if(isset($_POST['call'])){
    $call = $_POST['call'];
    $params = getParams('post', 'call');
}else if(isset($_GET['call'])){
    $call = $_GET['call'];
    $params = getParams('get', 'call');
}else if(isset($_POST['ajax'])){
    $call = $_POST['ajax'];
    $params = getParams('post', 'ajax');
}else if(isset($_GET['ajax'])){
    $call = $_GET['ajax'];
    $params = getParams('get', 'ajax');
}else{
    exit;
}

//if(!checkSecurityToken()) die("CSRF Attack");

//fillinfo();
if(@file_exists(DOKU_INC."lib/plugins/ownInit/init.php")){
    require_once (DOKU_INC."lib/plugins/ownInit/init.php");
    own_init();
}
if(existCommand($call)){
    print callCommand($call, $params);
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

function isUserAuthenticated(){
    global $_SERVER;
    return $_SERVER['REMOTE_USER'];
}

function existCommand($command){
    $file = DOKU_COMMANDS.$command.'/'.$command.'_command.php';
    $ret = false;
    if(@file_exists($file)){
        $ret=true;
        require_once($file);
    }
    return  $ret;
}

function callCommand($str_command, $arr_parameters){
    global $INFO;
    $str_command .= '_command';
    $command = new $str_command();
    
    $command->setParameters($arr_parameters);
    $ret = $command->run($INFO['userinfo']['grps']);
    if($command->error){
        $ret=$command->errorMessage;
    }
    return $ret;
}

function getParams($input, $without){
    global $JSINFO;
    $params = array();
    switch ($input){
        case 'post':
            foreach ($_POST as $key => $value){
                if($key!==$without || $key!==$JSINFO['sectokParamName']){
                    $params[$key]=$value;
                }
            }
        case 'get':
        default :
            foreach ($_GET as $key => $value){
                if($key!==$without || $key!==$JSINFO['sectokParamName']){
                    $params[$key]=$value;
                }
            }
    }
    return $params;    
}

