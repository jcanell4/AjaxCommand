<?php
if (!defined('DOKU_INC')) die();

/**
 * Class project_command: Llama a las Actions correspondientes por cada petición recibida en &do=
 * @culpable Rafael Claver
 */
class project_command extends abstract_project_command_class {

     public function __construct() {
        parent::__construct();
        $this->types[ProjectKeys::KEY_KEEP_DRAFT] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_NO_RESPONSE] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_CANCEL] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_CLOSE] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_TO_REQUIRE] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_LEAVERESOURCE] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_REFRESH] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_HAS_DRAFT] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_DISCARD_CHANGES] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_RECOVER_DRAFT] = self::T_BOOLEAN;
    }

    protected function process() {

        if (!$this->params[ProjectKeys::KEY_PROJECT_TYPE])
            throw new UnknownPojectTypeException();

        switch ($this->params[ProjectKeys::KEY_DO]) {

            case ProjectKeys::KEY_NEW_FOLDER:
                $action = $this->getModelManager()->getActionInstance("CreateFolderAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_NEW_DOCUMENT:
                $action = $this->getModelManager()->getActionInstance("CreateDocumentAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_DIFF:
                $action = $this->getModelManager()->getActionInstance("DiffProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_VIEW:
                $action = $this->getModelManager()->getActionInstance("ViewProjectMetaDataAction");
                $p = $this->params;
                $p[ProjectKeys::KEY_PROJECTTYPE_DIR] = $this->getKeyDataProject(ProjectKeys::KEY_PROJECTTYPE_DIR);
                $projectMetaData = $action->get($p);
                $this->_addExtraData($projectMetaData);
                if ($this->params[ProjectKeys::KEY_REV]) {
                    $projectMetaData['projectExtraData'][ProjectKeys::KEY_REV] = $this->params[ProjectKeys::KEY_REV];
                }
                break;

            case ProjectKeys::KEY_EDIT:
                if ($this->params[ProjectKeys::KEY_REFRESH]) {
                    $action = $this->getModelManager()->getActionInstance("RefreshProjectAction");
                }else {
                    $action = $this->getModelManager()->getActionInstance("GetProjectMetaDataAction");
                }
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_SAVE:
                $action = $this->getModelManager()->getActionInstance("SetProjectMetaDataAction");
                $this->params['extraProject']['old_autor'] = $this->getKeyDataProject('autor');
                $this->params['extraProject']['old_responsable'] = $this->getKeyDataProject('responsable');
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_CREATE_PROJET:
                $action = $this->getModelManager()->getActionInstance("CreateProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_CREATE_SUBPROJET:
                $action = $this->getModelManager()->getActionInstance("CreateSubprojectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_GENERATE:
                $action = $this->getModelManager()->getActionInstance("GenerateProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_CANCEL:
                $action = $this->getModelManager()->getActionInstance("CancelProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_REVERT:
                //Està en: wikiocmodel/projects/documentation/command/projectRevert.php
                break;

            case ProjectKeys::KEY_SAVE_PROJECT_DRAFT:
                $action = $this->getModelManager()->getActionInstance("DraftProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_REMOVE_PROJECT_DRAFT:
                throw new Exception("Excepció a project_command:[ ".ProjectKeys::KEY_REMOVE_PROJECT_DRAFT."]"); //[JOSEP] ALERTA: Caldria usar una exepció que hereti de WikiIocModelException!

            default:
                throw new UnknownProjectException();
        }

        if (!$projectMetaData) throw new UnknownProjectException();
        return $projectMetaData;

    }

    protected function getDefaultResponse($response, &$ret) {}

    private function _addExtraData(&$projectMetaData) {
        $projectMetaData['projectExtraData'] = [ProjectKeys::KEY_ROL => $this->authorization->getPermission()->getRol()];
        if ($projectMetaData[ProjectKeys::KEY_PROJECT_TYPE]) {
            $projectMetaData['projectExtraData'][ProjectKeys::KEY_PROJECT_TYPE] = $projectMetaData[ProjectKeys::KEY_PROJECT_TYPE];
        }else{
            $projectMetaData['projectExtraData'][ProjectKeys::KEY_PROJECT_TYPE] = $this->params[ProjectKeys::KEY_PROJECT_TYPE];
        }
    }
}
