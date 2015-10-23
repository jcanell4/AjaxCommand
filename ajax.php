<?php
/**
 * DokuWiki AJAX CALL SERVICE
 * Executa un command a partir de les dades rebudes a les variables $_POST o $_GET. Primer es comprova si
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

if(!defined('DOKU_INC')) define('DOKU_INC', dirname(__FILE__) . '/../../../');
require_once(DOKU_INC . 'inc/init.php');
require_once(DOKU_INC . 'inc/template.php');
if(!defined('DOKU_COMMANDS')) define('DOKU_COMMANDS', dirname(__FILE__) . '/commands/');
//close session
session_write_close();

header('Content-Type: text/html; charset=utf-8');

global $_SERVER;
global $_GET;
global $_POST;
global $_FILES;

$method = $_SERVER['REQUEST_METHOD'];
//call the requested function
if(isset($_POST['call'])) {
    $without = 'call';
    $call    = $_POST['call'];

} else if(isset($_GET['call'])) {
    $without = 'call';
    $call    = $_GET['call'];

} else if(isset($_POST['ajax'])) {
    $without = 'ajax';
    $call    = $_POST['ajax'];

} else if(isset($_GET['ajax'])) {
    $without = 'ajax';
    $call    = $_GET['ajax'];

} else {
    exit;
}
$params = getParams($without);

//if(!checkSecurityToken()) die("CSRF Attack");

//fillinfo();
if(@file_exists(DOKU_INC . "lib/plugins/ownInit/init.php")) {
    require_once(DOKU_INC . "lib/plugins/ownInit/init.php");
    own_init();
}

if(existCommand($call)) {
    print callCommand($call, $params);

} else {
    $dataEvent = array();
    $evt = new Doku_Event('CALLING_EXTRA_COMMANDS', $dataEvent);
    $evt->trigger();
    unset($evt);
    
    $noCommand=TRUE;
    if(sizeof($dataEvent)>0){
        $noCommand = !$dataEvent[$call]|| !existCommand($dataEvent[$call]["callFile"], TRUE);
    }
    if(!$noCommand){
        print callCommand($call, $params, $dataEvent[$call]["respHandlerDir"]);
    }else{
        if(!checkSecurityToken()) die("CSRF Attack");

        // Creem un evento Doku_Event amb
        $dataEvent = array('command' => $call, 'params' => $params);
        $evt       = new Doku_Event('AJAX_CALL_UNKNOWN', $dataEvent);
        if($evt->advise_before()) {
            print "AJAX call '" . htmlspecialchars($call) . "' unknown!\n";
            exit;
        }
        $evt->advise_after();
        unset($evt);
    }
}

/**
 *
 * @return string retorna el nom del usuari autenticat // TODO[Xavi] hauria de ser bool?
 */
function isUserAuthenticated() {
    global $_SERVER;
    return $_SERVER['REMOTE_USER'];
}

/**
 * Si existeix una classe command amb el nom passat com argument el carrega i retorna true, en cas contrari retorna
 * false.
 *
 * El fitxer que conté la classe ha d'estar dins d'una carpeta amb el nom del command, i el nom del fitxer estarà
 * compost pel nom del command afegint '_command.php'
 *
 * @param string $command command a carregar
 *
 * @return bool true si existeix un command amb aquest nom, o false en cas contrari.
 */
function existCommand($command, $isFile=FALSE) {
    if($isFile){
        $file = $command;
    }else{
        $file = DOKU_COMMANDS . $command . '/' . $command . '_command.php';
    }

    $ret  = FALSE;
    if(@file_exists($file)) {
        $ret = TRUE;
        require_once($file);
    }
    return $ret;
}

/**
 * Aquest mètode instancia un command amb el nom passat com argument, i li passa els paràmetres passats com argument.
 * Després el fa corre passant els grups als que pertany el usuari com a permissos.
 *
 * Intenta carregar un response_handler adequat pel command passat com argument. Els response handler s'han de trobar
 * dins de la carpeta del template, a una carpeta anomenada cmd_response_handler.
 *
 * El nom del handler pot ser igual al nom del command afegint '_response_handler.php' o en CamelCase afegint
 * 'ResponseHandler'.php, per exemple ioc-template/cmd_response_handler/CancelResponseHandler.php.
 *
 * @param string   $str_command    nom del command
 * @param string[] $arr_parameters hash amb els paràmetres per executar el command
 *
 * @return string el resultat de executar el command en format JSON o un missatge d'error
 */
function callCommand($str_command, $arr_parameters, $respHandDir=NULL) {
    global $INFO;
    $respHandObj = NULL;

    if(is_callable('tpl_incdir')) {
        $tplincdir = tpl_incdir();

    } else {
        $tplincdir = DOKU_TPLINC;
    }

    if(!$respHandDir){
        $respHandDir   = $tplincdir . 'cmd_response_handler/';
    }
    $respHandClass = $str_command . '_response_handler';
    $respHandFile  = $respHandDir . $respHandClass . '.php';

    if(!@file_exists($respHandFile)) {
        //CamelCase
        $respHandClass = strtoupper(substr($str_command, 0, 1))
            . strtolower(substr($str_command, 1))
            . 'ResponseHandler';
        $respHandFile  = $respHandDir . $respHandClass . '.php';
    }
    
    if(@file_exists($respHandFile)) {
        require_once($respHandFile);
        $respHandObj = new $respHandClass();
    }

    $str_command .= '_command';
    /** @var abstracT_command_class $command */
    $command = new $str_command();

    if($respHandObj) {
        $command->setResponseHandler($respHandObj);
    }
    $command->setParameters($arr_parameters);

    $ret = $command->run($INFO['userinfo']['grps']);

    if($command->error) {
        /**[TO DO] Controll exceptions**/
        if(is_object($command->error)){
            $ret = $command->errorMessage->getMessage();
        }else{
            header($command->errorMessage, TRUE, $command->error);
            $ret = $command->errorMessage;
        }
    }
    return $ret;
}

/**
 * Retorna un hash amb els paràmetres de $_GET, $_POST i $_FILE excepte el valor de la clau passada com argument.
 *
 * @param string $without clau que evitem extreure
 *
 * @return string[] hash amb els paràmetres
 */
function getParams($without) {
    global $JSINFO;
    $params = array();
    foreach($_GET as $key => $value) {
        if($key !== $without && $key !== $JSINFO['sectokParamName']) {
            $params[$key] = $value;
        }
    }
    foreach($_POST as $key => $value) {
        if($key !== $without && $key !== $JSINFO['sectokParamName']) {
            $params[$key] = $value;
        }
    }
    foreach($_FILES as $key => $value) {
        if($key !== $without && $key !== $JSINFO['sectokParamName']) {
            $params[$key] = $value;
        }
    }
    return $params;
}

