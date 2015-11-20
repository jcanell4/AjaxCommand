<?php
/**
 * DokuWiki AJAX REST SERVICE
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

if(!defined('DOKU_INC')) define('DOKU_INC', dirname(__FILE__) . '/../../../');
require_once(DOKU_INC . 'inc/init.php');
if(!defined('DOKU_COMMANDS')) define('DOKU_COMMANDS', dirname(__FILE__) . '/commands/');
if(!defined('REST_COMMAND_PARENT')) define('REST_COMMAND_PARENT', 'abstract_rest_command_class');
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

$method         = $_SERVER['REQUEST_METHOD'];
$request_params = NULL;

switch($method) {
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
$extra_url_params   = split('/', $_SERVER['PATH_INFO']); //TODO[Xavi] @deprecated split, canviar per explode() o splitreg()
$_REQUEST['sectok'] = $extra_url_params[SECTOK_PARAM];

//fillinfo();  //revisar si habría que usar el fillInfo de DokuModelAdapter

if(@file_exists(DOKU_INC . "lib/plugins/ownInit/init.php")) {
    require_once(DOKU_INC . "lib/plugins/ownInit/init.php");
    own_init();
}

if(array_key_exists($JSINFO['storeDataParamName'], $request_params)) {
    $command = $request_params[$JSINFO['storeDataParamName']];

} else if(isset($extra_url_params)) {
    $command          = $extra_url_params[1];
    $extra_url_params = array_slice($extra_url_params, 2);

} else {
    header('HTTP/1.1 400 Bad Request', TRUE, 400);
    exit;
}

if(existCommand($command)) {
    print callCommand($command, $method, $request_params, $extra_url_params);

} else {
    //checkSecurityToken(): revisar si habría que usar isSecurityTokenVerified() de DokuModelAdapter
    if(!checkSecurityToken()) die("CSRF Attack");

    $dataEvent = array('command' => $call, 'params' => $params);
    $evt       = new Doku_Event('AJAX_CALL_UNKNOWN', $dataEvent);
//                            array('command' => $call, 'params' => $params));

    if($evt->advise_before()) {
        print "AJAX call '" . htmlspecialchars($call) . "' unknown!\n";
        exit;
    }

    $evt->advise_after();
    unset($evt);
}
/**
 * Si existeix una classe command amb el nom passat com argument el carrega i retorna true, en cas contrari retorna
 * false.
 *
 * El fitxer que conté la classe ha d'estar dins d'una carpeta amb el nom del command, i el nom del fitxer estarà compost
 * pel nom del command afegint '_command.php'
 *
 * @param string $command command a carregar
 *
 * @return bool true si existeix un command amb aquest nom, o false en cas contrari.
 */
function existCommand($command) {
    $file = DOKU_COMMANDS . $command . '/' . $command . '_command.php';
    $ret  = FALSE;
    if(@file_exists($file)) {
        require_once($file);
        $class = new \ReflectionClass($command . '_command');

        if($class->getParentClass()->getName() === REST_COMMAND_PARENT) {
            $ret = TRUE;
        }
    }
    return $ret;
}

/**
 * Instancia un nou command i li envnia el mètode, els paràmetres extra i els permissos del usuari per processar-la.
 *
 * @param string   $str_command      nom del command a instanciar
 * @param string   $method           mètode pel que se ha rebut la petició
 * @param string[] $request_params   hash amb tots els paràmetres de la petició
 * @param string[] $extra_url_params hash amb els paràmetres extras de la petició (tots excepte el nom del command)
 *
 * @return string el resultat de executar el command en format JSON o un missatge d'error
 */

function callCommand($str_command, $method, $request_params, $extra_url_params) {
    //global $INFO;
    $str_command .= '_command';
    /** @var abstract_rest_command_class $command */
    $command = new $str_command();
    $command->setParameters($request_params);
    $ret = $command->dispatchRequest(
                   $method,
                   $extra_url_params
            );

    if($command->error) {
        $ret = $command->errorMessage;
    }

    return $ret;
}

//function fillinfo() {  //revisar si habría que usar el fillInfo de DokuModelAdapter
//    global $INFO;
//    $INFO = pageinfo();
//}