<?php
/**
 * DokuWiki AJAX CALL SERVICE
 * Executa un command a partir de les dades rebudes a les variables $_POST o $_GET.
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

if(!defined('DOKU_INC')) define('DOKU_INC', realpath(dirname(__FILE__) . '/../../../') . '/');
require_once(DOKU_INC . 'inc/init.php');
require_once(DOKU_INC . 'inc/template.php');
require_once(DOKU_INC . 'inc/pluginutils.php');
require_once(DOKU_INC . 'lib/plugins/ownInit/WikiGlobalConfig.php');

if(!defined('DOKU_COMMANDS')) define('DOKU_COMMANDS', dirname(__FILE__) . '/commands/');
if(!defined('SECTOK_PARAM')) define('SECTOK_PARAM', 0);

class ajaxCall {
    protected $call;
    protected $method;
    protected $commandClass;
    protected $request_params;
    protected $extra_url_params;

    public static function Instance() {
        static $inst = NULL;
        if($inst === NULL) {
            $inst = new ajaxCall();
            $inst->initialize();
        }
        return $inst;
    }

    private function __construct() {
        /*
         * Aquí se establece el valor por defecto del proyecto actual,
         * sin embargo, es necesario establecer un mecanismo para la asignación
         * dinámica de proyectos
         */
        global $plugin_controller;
        $plugin_controller->setCurrentProject('defaultProject');
    }

    public function initialize() {
        session_write_close();
        header('Content-Type: text/html; charset=utf-8');
    }

    public function setCommand() {
        global $_GET;
        global $_POST;

        if(isset($_POST['call'])) {
            $without    = 'call';
            $this->call = $_POST['call'];

        } else if(isset($_GET['call'])) {
            $without    = 'call';
            $this->call = $_GET['call'];

        } else if(isset($_POST['ajax'])) {
            $without    = 'ajax';
            $this->call = $_POST['ajax'];

        } else if(isset($_GET['ajax'])) {
            $without    = 'ajax';
            $this->call = $_GET['ajax'];
        }
        return $without;
    }

    public function loadOwn() {
        if (@file_exists(DOKU_INC . "lib/plugins/ownInit/init.php")) {
            require_once(DOKU_INC . "lib/plugins/ownInit/init.php");
            own_init();
        }
    }

    public function process() {
        if ($this->existCommand()) {
            print $this->callCommand();

        } else {
            $dataEvent = array();
            $evt = new Doku_Event('CALLING_EXTRA_COMMANDS', $dataEvent);
            $evt->trigger();
            unset($evt);

            $noCommand = TRUE;
            if (sizeof($dataEvent) > 0){
                $noCommand = !$dataEvent[$this->call] ||
                             !$this->existCommand($dataEvent[$this->call]["callFile"]);
            }
            if (!$noCommand){
                print $this->callCommand($dataEvent[$this->call]["respHandlerDir"]);
            }else{
                //revisar si habría que usar isSecurityTokenVerified() de DokuModelAdapter
                if (!checkSecurityToken()) die("CSRF Attack");

                $dataEvent = array('command' => $this->call, 'params' => $this->request_params);
                $evt       = new Doku_Event('AJAX_CALL_UNKNOWN', $dataEvent);
                if ($evt->advise_before()) {
                    print "AJAX call '" . htmlspecialchars($this->call) . "' unknown!\n";
                } else {
                    $evt->advise_after();
                    unset($evt);
                }
            }
        }
    }

    /**
     * Si existeix una classe command amb el nom passat com argument el carrega i retorna true,
     * en cas contrari retorna false.
     * El fitxer que conté la classe ha d'estar dins d'una carpeta amb el nom del command,
     * i el nom del fitxer estarà compost pel nom del command afegint '_command.php'
     *
     * @param string $command command a carregar
     *
     * @return bool true si existeix un command amb aquest nom, o false en cas contrari.
     */
    function existCommand($file=NULL) {
        //'commands' definits a l'estructura 'ajaxcommand'
        if (!$file){
            $file = DOKU_COMMANDS . $this->call . '/' . $this->call . '_command.php';
        }
        if (($ret = @file_exists($file))) {
            require_once($file);
            $this->commandClass = $this->call . '_command';
        }

        //'commands' definits a altres plugins
        if (!$ret) {
            $pluginList = plugin_list('command');
            $DOKU_PLUGINS = DOKU_INC . "lib/plugins/";
            foreach ($pluginList as $plugin) {
                $p = explode('_', $plugin, 3);
                if ($p[1] == 'projects') { //fa referencia a un projecte dins del plugin
                    $c = explode('_', $p[2], 2);
                    if (count($c) === 2) {  //es un fichero del directorio 'command'
                        $file = "$DOKU_PLUGINS{$p[0]}/{$p[1]}/{$c[0]}/command/{$c[1]}.php";
                        $nom = "{$p[0]}_{$p[1]}_{$c[0]}_{$c[1]}";
                    }else {
                        $file = "$DOKU_PLUGINS{$p[0]}/{$p[1]}/{$c[0]}/command.php";
                        $nom = "{$p[0]}_{$p[1]}_{$c[0]}_command";
                    }
                }else {
                    $p = explode('_', $plugin, 2);
                    if (count($p) === 2) {
                        $file = "$DOKU_PLUGINS{$p[0]}/command/{$p[1]}.php";
                        $nom = "{$p[0]}_{$p[1]}";
                    }else {
                        $file = "$DOKU_PLUGINS{$p[0]}/command.php";
                        $nom = "{$p[0]}_command";
                    }
                }
                if ($nom == $this->call) {
                    if (($ret = @file_exists($file))) {
                        require_once($file);
                        $this->commandClass = $nom;
                        return $ret;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Aquest mètode instancia un command amb el nom definit a $this->call.
     * Intenta carregar un response_handler adequat al command. Si no s'indica com a paràmetre,
     * els response handler s'han de trobar a una carpeta del template anomenada cmd_response_handler.
     *
     * El nom del handler pot ser igual al nom del command afegint '_response_handler.php' o en CamelCase afegint
     * 'ResponseHandler'.php, per exemple ioc-template/cmd_response_handler/CancelResponseHandler.php.
     *
     * @param string $respHandDir directori alternatiu dels ResponseHandler
     * @return string el resultat de executar el command en format JSON o un missatge d'error
     */
    function callCommand( $respHandDir=NULL ) {
        $respHandObj = NULL;

        $tplincdir = WikiGlobalConfig::tplIncDir();

        if (!$respHandDir){
            $respHandDir = $tplincdir . 'cmd_response_handler/';
        }
        $respHandClass = $this->call . '_response_handler';
        $respHandFile  = $respHandDir . $respHandClass . '.php';

        if (!@file_exists($respHandFile)) {
            $respHandClass = $this->camelCase($this->call, 'ResponseHandler');
            $respHandFile  = $respHandDir . $respHandClass . '.php';
        }

        if (@file_exists($respHandFile)) {
            require_once($respHandFile);
            $respHandObj = new $respHandClass();
        }

        $str_command = $this->commandClass;
        $command = new $str_command();
        $command->setParameters($this->request_params);
        if ($this->extra_url_params) {
            $command->setParamValuesFromUrl($this->extra_url_params);
        }
        $command->init();

        if ($respHandObj) {
            $command->setResponseHandler($respHandObj);
        }

        $ret = $command->run();

        if ($command->error) {
            if (is_object($command->error)) {
                $ret = $command->error->getMessage();
            }else {
                header($command->errorMessage, TRUE, $command->error);
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
        global $_GET;
        global $_POST;
        global $_FILES;
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

    public function setRequestParams($request_params) {
        $this->request_params = $request_params;
    }

    function camelCase($str, $extra) {
        return strtoupper(substr($str, 0, 1)) . strtolower(substr($str, 1)) . $extra;
    }
}

/**
 * DokuWiki AJAX REST SERVICE
*/
class ajaxRest extends ajaxCall {

    public static function Instance() {
        static $inst = NULL;
        if($inst === NULL) {
            $inst = new ajaxRest();
        }
        return $inst;
    }

    private function __construct() {
        global $_SERVER;
        parent::Instance();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function requestHtmlParams() {
        global $_GET;
        global $_POST;
        global $_SERVER;
        global $_REQUEST;

        switch ($this->method) {
            case 'GET':
            case 'HEAD':
                $this->request_params = $_GET;
                break;
            case 'POST':
                $this->request_params = $_POST;
                break;
            case 'PUT':
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $this->request_params);
                break;
        }
        $this->extra_url_params = explode('/', $_SERVER['PATH_INFO']);
        $this->request_params['method'] = $this->method;
        $_REQUEST['sectok']     = $this->extra_url_params[2];
    }

    public function setCommand() {
        global $JSINFO;
        $ret = TRUE;
        if (array_key_exists($JSINFO['storeDataParamName'], $this->request_params)) {
            $this->call = $this->request_params[$JSINFO['storeDataParamName']];

        } else if(isset($this->extra_url_params)) {
            $this->call             = $this->extra_url_params[1];
            $this->extra_url_params = array_slice($this->extra_url_params, 2);

        } else {
            header('HTTP/1.1 400 Bad Request', TRUE, 400);
            $ret = FALSE;
        }
        return $ret;
    }

    /**
     * Instancia un nou command i li envia el mètode, els paràmetres extra i els permissos del usuari per processar-la.
     *
     * @param string   $str_command      nom del command a instanciar
     * @param string   $method           mètode pel que se ha rebut la petició
     * @param string[] $request_params   hash amb tots els paràmetres de la petició
     * @param string[] $extra_url_params hash amb els paràmetres extras de la petició (tots excepte el nom del command)
     *
     * @return string el resultat de executar el command en format JSON o un missatge d'error
     */

//    function callCommand( $respHandDir=NULL ) {
//        $str_command = $this->call . '_command';
//        $command = new $str_command();
//        $command->setParameters($this->request_params);
//        $command->setParamValuesFromUrl($this->extra_url_params);
//        $command->init();
//
//        $ret = $command->dispatchRequest(
//                       $this->method
//                       /*,$extra_url_params //a setParamValuesFromUrl */
//                );
//        if($command->error) {
//            $ret = $command->errorMessage;
//        }
//        return $ret;
//    }

}
