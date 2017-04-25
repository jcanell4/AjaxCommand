<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC . "lib/plugins/ajaxcommand/");

require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'defkeys/ProjectKeys.php');

class project_command extends abstract_command_class {

    private $dataProject;   //guarda los datos del proyecto para verificar la autorización
    private $persistenceEngine;

    public function __construct() {
        parent::__construct();
        $this->types[ProjectKeys::KEY_ID] = abstract_command_class::T_STRING;
        $this->types[ProjectKeys::KEY_DO] = abstract_command_class::T_STRING;

        $defaultValues = [ProjectKeys::KEY_DO => ProjectKeys::KEY_EDIT];
        $this->setParameters($defaultValues);
    }

    public function init( $modelManager = NULL ) {
        parent::init($modelManager);
        $this->persistenceEngine = $this->modelWrapper->getPersistenceEngine();
        $projectMetaDataQuery = $this->persistenceEngine->createProjectMetaDataQuery();
        $ns = ($this->params[ProjectKeys::KEY_NS]) ? $this->params[ProjectKeys::KEY_NS] : $this->params[ProjectKeys::KEY_ID];
        $this->dataProject = $projectMetaDataQuery->getDataProject($ns, $this->params[ProjectKeys::KEY_PROJECT_TYPE]);
    }

    protected function process() {

        if (!$this->params[ProjectKeys::KEY_PROJECT_TYPE])
            throw new UnknownPojectTypeException();

        switch ($this->params[ProjectKeys::KEY_DO]) {
            case ProjectKeys::KEY_EDIT:
                $action = new GetProjectMetaDataAction($this->persistenceEngine);
                $projectMetaData = $action->get($this->params);
                $extra = [ProjectKeys::KEY_PROJECT_TYPE => $this->params[ProjectKeys::KEY_PROJECT_TYPE],
                          ProjectKeys::KEY_ROL          => $this->authorization->getPermission()->getRol()];
                $projectMetaData['projectExtraData'] = $extra;
                break;

            case ProjectKeys::KEY_SAVE:
                $action = new SetProjectMetaDataAction($this->persistenceEngine);
                $parms['dataProject'] = $this->params;
                $parms['extraProject']['old_autor'] = $this->dataProject['autor'];
                $parms['extraProject']['old_responsable'] = $this->dataProject['responsable'];
                $projectMetaData = $action->get($parms);
                break;

            case ProjectKeys::KEY_CREATE:
                $action = new CreateProjectMetaDataAction($this->persistenceEngine);
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_GENERATE:
                $action = new GenerateProjectMetaDataAction($this->persistenceEngine);
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

    public function getKeyDataProject($key=NULL) {
        return ($key) ? $this->dataProject[$key] : $this->dataProject;
    }

    public function getAuthorizationType() {
        $dokey = $this->params[ProjectKeys::KEY_DO];
        switch ($dokey) {
            case ProjectKeys::KEY_EDIT:
            case ProjectKeys::KEY_CREATE:
            case ProjectKeys::KEY_GENERATE:
            case ProjectKeys::KEY_SAVE:
                $dokey .= "Project";
                break;
            default:
                $dokey = "admin";
        }
        return $dokey;
    }

    /**
     * Afegeix la pàgina passada com argument com una resposta de tipus DATA_TYPE al generador de respostes.
     * @param array $response amb el contingut de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     * @return mixed|void
     */
    protected function getDefaultResponse($response, &$ret) {}

}
