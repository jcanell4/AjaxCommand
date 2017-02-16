<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC . "lib/plugins/ajaxcommand/");

require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'requestparams/PageKeys.php');
require_once(DOKU_COMMAND . 'requestparams/RequestParameterKeys.php');

class project_command extends abstract_command_class {

    private $dataProject;   //guarda los datos del proyecto para verificar la autorización
    
    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = abstract_command_class::T_STRING;
        $this->types[RequestParameterKeys::DO_KEY] = abstract_command_class::T_STRING;

        $defaultValues = [RequestParameterKeys::DO_KEY => 'edit'];
        $this->setParameters($defaultValues);
    }

    public function init( $modelManager = NULL ) {
        parent::init($modelManager);
        $persistenceEngine = $this->modelWrapper->getPersistenceEngine();
        $projectMetaDataQuery = $persistenceEngine->createProjectMetaDataQuery();
        $ns = ($this->params['ns']) ? $this->params['ns'] : $this->params['id'];
        $this->dataProject = $projectMetaDataQuery->getDataProject($ns, $this->params['projectType']);
    }
    
    protected function process() {
        
        if (!$this->params[RequestParameterKeys::PROJECT_TYPE])
            throw new UnknownPojectTypeException();
        
        switch ($this->params[RequestParameterKeys::DO_KEY]) {
            case 'edit':
                $action = new GetProjectMetaDataAction($this->modelWrapper->getPersistenceEngine());
                $projectMetaData = $action->get($this->params);
                break;

            case 'save':
                $action = new SetProjectMetaDataAction($this->modelWrapper->getPersistenceEngine());
                $projectMetaData = $action->get($this->params);
                break;

            case 'create':
                $action = new CreateProjectMetaDataAction($this->modelWrapper->getPersistenceEngine());
                $projectMetaData = $action->get($this->params);
                break;

            case 'generate':
                $action = new GenerateProjectMetaDataAction($this->modelWrapper->getPersistenceEngine());
                $projectMetaData = $action->get($this->params);
                break;

            default:
                throw new UnknownProjectException();
        }

        if ($projectMetaData)
            return $projectMetaData;
        else
            throw new UnknownProjectException();
    }

    public function getKeyDataProject($key) {
        return ($key) ? $this->dataProject[$key] : $this->dataProject;
    }

    public function getAuthorizationType() {
        $dokey = $this->params[RequestParameterKeys::DO_KEY];
        switch ($dokey) {
            case 'edit':
            case 'create':
            case 'generate':
            case 'save':
                $dokey .= "Project";
                break;
            default:
                $dokey = "admin";
        }
        return $dokey;
    }

    /**
     * Afegeix la pàgina passada com argument com una resposta de tipus DATA_TYPE al generador de respostes.
     *
     * @param array $response amb el contingut de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     *
     * @return mixed|void
     */
    protected function getDefaultResponse($response, &$ret) {}

}
