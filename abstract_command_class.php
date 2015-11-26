<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('WIKI_IOC_MODEL')) define('WIKI_IOC_MODEL', DOKU_PLUGIN . 'wikiiocmodel/');
require_once(DOKU_PLUGIN . 'ajaxcommand/AbstractResponseHandler.php');
require_once(DOKU_INC . 'inc/plugin.php');
require_once(WIKI_IOC_MODEL . 'WikiIocModelManager.php');

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

    //protected $content = ''; no utilizado
    public $error = FALSE;
    public $errorMessage = '';
    public $throwsException = FALSE;

    /**
     * Constructor en el que s'assigna un nou DokuModelAdapter a la classe
     */
    public function __construct($modelManager=NULL) {
        if($modelManager){
            $this->setModelManager($modelManager);
        }else{
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
        $this->modelWrapper  = $modelManager->getModelWrapperManager();
        $this->authorization = $modelManager->getAuthorizationManager($this->getNameCommandClass(), $this);
        // PROVES DE PERMISSOS
//        $permission = $this->authorization->getPermission();
//        if ($permission->getIsAuthorized()) {
//            $permission->setIsAuthorized(FALSE);
//        }else{
//            $permission->setIsAuthorized(TRUE);
//        }
    }
    
    /**
     * @return string (nom del command a partir del nom de la clase)
     */
    public function getNameCommandClass() {
        return preg_replace('/_command$/', '', get_class($this));
    }

    public function getParams() {
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
        if(!$respHand->getAuthorization()){
            $respHand->setAuthorization($this->authorization);
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
                        && $this->types[$key]!= self::T_FUNCTION
                        && $this->types[$key]!= self::T_METHOD
                        && $this->types[$key]!= self::T_FILE                    
                        && gettype($value) != $this->types[$key]) {
                settype($value, $this->types[$key]);
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
     * @param mixed $params Parámetros extra para permisos adicionales
     *          Anteriormente string[]|null $grup hash amb els permissos.
     *          Corresponia a $INFO[userinfo][grps] de la DokuWiki -> run($grup = NULL)
     * @return string|null resposta de executar el command en format JSON
     * @throws Exception si no es té autorització
     */
    public function run($params) {
        $ret = NULL;
        $permission = $this->authorization->getPermission($params);
        if ($this->authorization->canRun($permission)) {
            $ret = $this->getResponse();
            
            if($permission->isDenied()) {
                $this->error        = 403;
                $this->errorMessage = "permission denied"; /*TODO internacionalització */
            }
        } else {
            $this->error        = 403;
            $this->errorMessage = "permission denied"; /*TODO internacionalització */
        }
        if($this->error && $this->throwsException) {
            throw new Exception($this->errorMessage);
        }
        return $ret;
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
    protected function getResponse() {
        $ret = new AjaxCmdResponseGenerator();
        try{
            $response = $this->process();

            if($this->getResponseHandler()) {
                $this->getResponseHandler()->processResponse($this->params, $response, $ret);
            } else {
                $this->getDefaultResponse($response, $ret);
            }
        }  catch (HttpErrorCodeException $e){
            $this->error = $e->getCode();
            $this->errorMessage = $e->getMessage();
        }  catch (Exception $e){
            if($this->getErrorHandler()) {
                $this->getErrorHandler()->processResponse($this->params, $e, $ret);
            } else {
                $this->getDefaultErrorResponse($this->params, $e, $ret);
            }            
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
    protected abstract function process();
}