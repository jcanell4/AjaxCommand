<?php
/**
 * DokuWiki AJAX CALL SERVICE
 *
 * @author     Josep Cañellas <jcanell4@ioc.cat>
 */


if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/../../../');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/template.php');
if(!defined('DOKU_COMMANDS')) define('DOKU_COMMANDS',dirname(__FILE__).'/commands/');
//close session
session_write_close();


header('Content-Type: text/html; charset=utf-8');

global $_SERVER;
global $_GET;
global $_POST;

$method = $_SERVER['REQUEST_METHOD'];
//call the requested function
if(isset($_POST['call'])){
    $without = 'call';
    $call = $_POST['call'];
}else if(isset($_GET['call'])){
    $without = 'call';
    $call = $_GET['call'];
}else if(isset($_POST['ajax'])){
    $without = 'ajax';
    $call = $_POST['ajax'];
}else if(isset($_GET['ajax'])){
    $without = 'ajax';
    $call = $_GET['ajax'];
}else{
    exit;
}
$params = getParams($without);


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
    $respHandObj;
    
    if(is_callable('tpl_incdir')){
        $tplincdir=  tpl_incdir();
    }else{
        $tplincdir = DOKU_TPLINC;
    }
    $respHandDir = $tplincdir.'cmd_response_handler/';
    $respHandClass = $str_command.'_response_handler';
    $respHandFile = $respHandDir.$respHandClass.'.php';
    if(@file_exists($respHandFile)){
        require_once($respHandFile);
        $respHandObj = new $respHandClass();
    }else{
        //CamelCase
        $respHandClass = strtoupper(substr($str_command, 0,1))
                                .strtolower(substr($str_command, 1))
                                .'ResponseHandler';
        $respHandFile = $respHandDir.$respHandClass.'.php';
        if(@file_exists($respHandFile)){
            require_once($respHandFile);
            $respHandObj = new $respHandClass();
        }else{
            $respHandObj=NULL;
        }        
    }
    $str_command .= '_command';
    $command = new $str_command();
    
    if($respHandObj){
        $command->setResponseHandler($respHandObj);
    }    
    $command->setParameters($arr_parameters);
    
    $ret = $command->run($INFO['userinfo']['grps']);
    
    if($command->error){
        $ret=$command->errorMessage;
    }
    return $ret;
}

function getParams($without){
    global $JSINFO;
    $params = array();
    foreach ($_GET as $key => $value){
        if($key!==$without || $key!==$JSINFO['sectokParamName']){
            $params[$key]=$value;
        }
    }
    foreach ($_POST as $key => $value){
        if($key!==$without || $key!==$JSINFO['sectokParamName']){
            $params[$key]=$value;
        }
    }
    /*
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
    */
    return $params;    
}

