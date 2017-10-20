<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC . "lib/plugins/ajaxcommand/");
require_once(DOKU_COMMAND . "abstract_project_command_class.php");
require_once(DOKU_COMMAND . "defkeys/ProjectKeys.php");

class project_command extends abstract_project_command_class {

    protected function process() {

        if (!$this->params[ProjectKeys::KEY_PROJECT_TYPE])
            throw new UnknownPojectTypeException();

        switch ($this->params[ProjectKeys::KEY_DO]) {
            case ProjectKeys::KEY_EDIT:
                $action = new GetProjectMetaDataAction($this->persistenceEngine);
                $projectMetaData = $action->get($this->params);
                $projectMetaData['projectExtraData'] = [ProjectKeys::KEY_PROJECT_TYPE => $this->params[ProjectKeys::KEY_PROJECT_TYPE],
                                                        ProjectKeys::KEY_ROL          => $this->authorization->getPermission()->getRol()];
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
                $projectMetaData['projectExtraData'] = [ProjectKeys::KEY_PROJECT_TYPE => $this->params[ProjectKeys::KEY_PROJECT_TYPE],
                                                        ProjectKeys::KEY_ROL          => $this->authorization->getPermission()->getRol()];
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

    /**
     * Afegeix la pàgina passada com argument com una resposta de tipus DATA_TYPE al generador de respostes.
     * @param array $response amb el contingut de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     * @return mixed|void
     */
    protected function getDefaultResponse($response, &$ret) {}

}
