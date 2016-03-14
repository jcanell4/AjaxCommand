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
require_once(DOKU_INC . 'lib/plugins/ownInit/WikiGlobalConfig.php');

class pageState {
    protected $call;
    protected $method;
    protected $request_params;
    protected $extra_url_params;
    
    private function __construct() {
        global $_SERVER;
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public static function Instance() {
        static $inst = NULL;
        if($inst === NULL) {
            $inst = new pageState();
            $inst->initialize();
        }
        return $inst;
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

    /**
     * Retorna un hash amb els paràmetres de $_GET, $_POST i $_FILE excepte el valor de la clau passada com argument.
     * @param string $without clau que evitem extreure
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

    /**
     * DokuWiki AJAX REST SERVICE
    */
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
        $_REQUEST['sectok']     = $this->extra_url_params[SECTOK_PARAM];
    }

}
