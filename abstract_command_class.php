<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once(DOKU_INC . 'inc/plugin.php');
require_once(DOKU_PLUGIN . 'ajaxcommand/AbstractResponseHandler.php');
require_once(DOKU_PLUGIN . 'wikiiocmodel/WikiIocModelManager.php');

/**
 * Class abstract_command_class
 *
 * Classe abstracta a partir de la que hereten els altres commands.
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
abstract class abstract_command_class extends DokuWiki_Plugin {
    const T_BOOLEAN  = "boolean";
    const T_INTEGER  = "integer";
    const T_DOUBLE   = "double";
    const T_FLOAT    = "float";
    const T_STRING   = "string";
    const T_ARRAY    = "array";
    const T_OBJECT   = "object";
    const T_FUNCTION = "function";
    const T_METHOD   = "method";
    const T_FILE     = "file";

    protected static $PLUGUIN_TYPE = 'command';
    protected static $FILENAME_PARAM = 'name';
    protected static $FILE_TYPE_PARAM = 'type';
    protected static $ERROR_PARAM = 'error';
    protected static $FILE_CONTENT_PARAM = 'tmp_name';

    protected $responseHandler = NULL;
    protected $errorHandler = NULL;

    protected $params = array();
    protected $types = array();
    protected $permissionFor = array();
    protected $authenticatedUsersOnly = TRUE;
    protected $runPreprocess = FALSE;
    
    protected $authorization;
    protected $modelWrapper;

    public $error = FALSE;
    public $errorMessage = '';

    public function __construct( $modelWrapper=NULL, $authorization=NULL ) {
        $this->modelWrapper  = $modelWrapper;
        $this->authorization = $authorization;
    }

    /**
     * Constructor en el que s'assigna un nou DokuModelAdapter a la classe
     */
    public function init( $modelManager = NULL ) {
        if ($modelManager) {
            $this->setModelManager($modelManager);
        } else {
            $this->setModelManager(WikiIocModelManager::Instance());
        }
    }

    /**
     * Retorna l'adaptador a emprar.
     * @return DokuModelAdapter
     */
    public function getModelWrapper() {
        return $this->modelWrapper;
    }
    
    public function getAuthorization() {
        return $this->authorization;
    }
    
    /**
     * Estableix l'adaptador a emprar i l'autorització que li correspon.
     * @param modelManager
     */
    public function setModelManager($modelManager) {
        if(!$this->modelWrapper){
            $this->modelWrapper  = $modelManager->getModelWrapperManager();
        }
        if(!$this->authorization){
            $this->authorization = $modelManager->getAuthorizationManager($this->getAuthorizationType(), $this);
        }
    }
    
    /**
     * @return string (nom del command a partir del nom de la clase)
     */
    public function getAuthorizationType() {
        $className = preg_replace('/_command$/', '', get_class($this));
        return $className;
    }

    public function getParams($key=NULL) {
        if ($key)
            return $this->params[$key];
        else
            return $this->params;
    }

    public function getTypes() {
        return $this->types;
    }

    public function getRunPreprocess() {
        return $this->runPreprocess;
    }

    public function getPermissionFor() {
        return $this->permissionFor;
    }

    protected function setPermissionFor($permissionFor) {
        $this->permissionFor = $permissionFor;
    }
  
    public function getAuthenticatedUsersOnly() {
        return $this->authenticatedUsersOnly;
    }

    /**
     * @param AbstractResponseHandler $respHand
     */
    public function setResponseHandler($respHand) {
        $this->responseHandler = $respHand;
        if(!$respHand->getModelWrapper()){
            $respHand->setModelWrapper($this->modelWrapper);
        }
    }

    /**
     * @return AbstractResponseHandler
     */
    public function getResponseHandler() {
        return $this->responseHandler;
    }

    public function setErrorHandler($errorHand) {
        $this->errorHandler = $errorHand;
    }

    public function getErrorHandler() {
        return $this->errorHandler;
    }

    /**
     * @param bool $onoff
     */
    public function setThrowsException($onoff) {
        $this->throwsException = $onoff;
    }

    /**
     * @param string[] $types
     */
    protected function setParameterTypes($types) {
        $this->types = $types;
    }

    /**
     * @param string[] $defaultValue
     */
    protected function setParameterDefaultValues($defaultValue) {
        $this->setParameters($defaultValue);
    }

    /**
     * @param string[] $params hash amb els paràmetres
     */
    public function setParameters($params) {
        foreach($params as $key => $value) {
            if(isset($this->types[$key])
                        && $this->types[$key]!= self::T_OBJECT
                        && $this->types[$key]!= self::T_ARRAY
                        && $this->types[$key]!= self::T_FUNCTION
                        && $this->types[$key]!= self::T_METHOD
                        && $this->types[$key]!= self::T_FILE                    
                        && gettype($value) != $this->types[$key]) {
                settype($value, $this->types[$key]);
            }else if(isset($this->types[$key])
                        && $this->types[$key]== self::T_ARRAY
                        && gettype($value) == self::T_STRING){
                $value = explode(',', $value);
            }else if(isset($this->types[$key])
                        && $this->types[$key]== self::T_OBJECT
                        && gettype($value) == self::T_STRING){
                $value = json_decode($value);
            }else if(isset($this->types[$key])
                        && ($this->types[$key]== self::T_FUNCTION
                                || $this->types[$key]== self::T_METHOD
                       )&& gettype($value) != self::T_STRING){
                settype($value, self::T_STRING);                
            }else if(isset($this->types[$key])
                        && $this->types[$key]== self::T_FILE
                        && gettype($value) != self::T_ARRAY){
                settype($value, self::T_ARRAY);                
            }
            $this->params[$key] = $value;
        }
    }

    /**
     * Comproba si la comanda la pot executar tothom i si no es així si s'ha verificat el token de seguretat,
     * si l'usuari està autenticat i si està autoritzat per fer corre la comanda. Si es aixi la executa i en cas
     * contrari llença una excepció.
     *
     * @return string|null resposta de executar el command en format JSON
     * @throws Exception si no es té autorització
     */
    public function run() {
        $ret = NULL;
        $permission = $this->authorization->getPermission($this);
        $retAuth = $this->authorization->canRun($permission);
        if ($retAuth) {
            $ret = $this->getResponse($permission);
        } else {
            $e = $this->authorization->getAuthorizationError('exception');
            $responseGenerator = new AjaxCmdResponseGenerator();
            $this->handleError(new $e(), $responseGenerator);
            $ret = $responseGenerator->getResponse();
        }
        return $ret;
    }
    
    protected function handleError($e, &$responseGenerator){
        if ($e->getCode() >= 1000){
            $error_handler = $this->getErrorHandler();
            if ($error_handler) {
                $error_handler->processResponse($this->params, $e, $responseGenerator);
            } else {
                $this->getDefaultErrorResponse($this->params, $e, $responseGenerator);
            }            
        }
    }

    /**
     * Processa la comanda, si hi ha un ResponseHandler se li pasa els paràmetres, la resposta i el
     * AjaxCmdResponseGenerator, si no hi ha es pasa es crida al métode per obtenir la resposta per defecte amb el
     * AjaxCmdResponseGenerator i la resposta.
     *
     * La resposta es passa per referencia, de manera que es modificada als métodes processResponse/getDefaultResponse.
     *
     * @return string resposta processada en format JSON
     */
    protected function getResponse($permission) {
        $ret = new AjaxCmdResponseGenerator();
        try {
            $response = $this->process();
            $response_handler = $this->getResponseHandler();

            if ($response_handler) {
                $response_handler->setPermission($permission);
                $response_handler->processResponse($this->params, $response, $ret);
            } else {
                $this->getDefaultResponse($response, $ret);
            }
        } catch (HttpErrorCodeException $e){
            $this->error        = $e->getCode();
            $this->errorMessage = $e->getMessage();
        } catch (Exception $e){
            $this->handleError($e, $ret);
        }

        $jsonResponse = $ret->getResponse();

        // for a dojo iframe the json response has to be inside a textarea 
        if (isset($this->params['iframe'])) {
          $jsonResponse = "<html><body><textarea>" . $jsonResponse . "</textarea></body></html>";   
       }
        return $jsonResponse;
    }

    /**
     * Retorna la resposta per defecte del command.
     *
     * @param mixed                    $response
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return mixed
     */
    protected abstract function getDefaultResponse($response, &$responseGenerator);

    /**
     * Retorna la resposta per defecte quan el process llença una excepció.
     * Aquest mètode s'executarà només en cas que la comanda no disposi de cap 
     * objecte errorHandler (de tipus ResponseHandler).
     *
     * @param Exception                    $response
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return mixed
     */
    protected function getDefaultErrorResponse($params, $e, &$ret){
        $ret->addError($e->getCode(), $e->getMessage());
    }

    /**
     * Retorna el tipus de plugin.
     *
     * @return string
     */
    public function getPluginType() {
        return self::$PLUGUIN_TYPE;
    }

    /**
     * Retorna el nom del plugin.
     *
     * @return string
     */
    public function getPluginName() {
        $dirPlugin = realpath($this->getClassDirName() . '/../..');
        if($dirPlugin) {
            $dir = substr($dirPlugin, -11);
            if($dir && $dir === "ajaxcommand") {
                $ret = "ajaxcommand";
            } else {
                $ret = parent::getPluginName();
            }
        } else {
            $ret = parent::getPluginName();
        }
        return $ret;
    }

    /**
     * Retorna el nom del component.
     *
     * @return string
     */
    public function getPluginComponent() {
        //TODO[Xavi] split està @deprecated, substituir per explode()
        $dirs   = split("/", $this->getClassDirName());
        $length = sizeof($dirs);
        if($length > 2) {
            $dir = substr($dirs[$length - 3], -11);
            if($dir && $dir === "ajaxcommand") {
                $ret = $dirs[$length - 1];
            } else {
                $ret = parent::getPluginName();
            }
        } else {
            $ret = parent::getPluginName();
        }
        return $ret;
    }

    /**
     * Retorna el nom del directori on es troba la classe.
     *
     * @return string
     */
    private function getClassDirName() {
        $thisClass = new ReflectionClass($this);
        return dirname($thisClass->getFileName());
    }

    /**
     * Processa el command.
     *
     * @return mixed varia segons la implementació del command
     */
    protected abstract function process($permission=NULL);
}
