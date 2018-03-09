<?php
if (!defined('DOKU_INC')) die();

/**
 * Class project_command: Llama a las Actions correspondientes por cada petición recibida en &do=
 * @culpable Rafael Claver
 */
class project_command extends abstract_project_command_class {

    protected function process() {

        if (!$this->params[ProjectKeys::KEY_PROJECT_TYPE])
            throw new UnknownPojectTypeException();

        switch ($this->params[ProjectKeys::KEY_DO]) {

            case ProjectKeys::KEY_EDIT:
                $action = $this->getModelManager()->getActionInstance("GetProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                $projectMetaData['projectExtraData'] = [ProjectKeys::KEY_PROJECT_TYPE => $this->params[ProjectKeys::KEY_PROJECT_TYPE],
                                                        ProjectKeys::KEY_ROL          => $this->authorization->getPermission()->getRol()];
                break;

            case ProjectKeys::KEY_SAVE:
                $action = $this->getModelManager()->getActionInstance("SetProjectMetaDataAction");
                $parms['dataProject'] = $this->params;
                $parms['extraProject']['old_autor'] = $this->dataProject['autor'];
                $parms['extraProject']['old_responsable'] = $this->dataProject['responsable'];
                $projectMetaData = $action->get($parms);
                break;

            case ProjectKeys::KEY_CREATE:
                $action = $this->getModelManager()->getActionInstance("CreateProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                $projectMetaData['projectExtraData'] = [ProjectKeys::KEY_PROJECT_TYPE => $this->params[ProjectKeys::KEY_PROJECT_TYPE],
                                                        ProjectKeys::KEY_ROL          => $this->authorization->getPermission()->getRol()];
                break;

            case ProjectKeys::KEY_GENERATE:
                $action = $this->getModelManager()->getActionInstance("GenerateProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_CANCEL:
                $action = $this->getModelManager()->getActionInstance("CancelProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_SAVE_PROJECT_DRAFT:
                $action = $this->getModelManager()->getActionInstance("DraftProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_REMOVE_PROJECT_DRAFT:
                throw new Exception("Excepció a project_command:[ ".ProjectKeys::KEY_REMOVE_PROJECT_DRAFT)."]";

            default:
                throw new UnknownProjectException();
        }

        if ($projectMetaData)
            return $projectMetaData;
        else
            throw new UnknownProjectException();
    }

    protected function getDefaultResponse($response, &$ret) {}

}
