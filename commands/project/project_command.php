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
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                if ($this->params[ProjectKeys::KEY_REV]) {
                    $projectMetaData[ProjectKeys::KEY_PROJECT_EXTRADATA][ProjectKeys::KEY_REV] = $this->params[ProjectKeys::KEY_REV];
                }
                break;

            case ProjectKeys::KEY_PARTIAL: // Fallthrough intencionat
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
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_CREATE_PROJECT:
                $action = $this->getModelManager()->getActionInstance("CreateProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_CREATE_SUBPROJECT:
                //Está en: lib/plugins/ajaxcommand/commands/create_subproject/create_subproject_command.php
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
                //Està en: <en_algún_plugin>/projects/documentation/command/projectRevert.php
                break;

            case ProjectKeys::KEY_SAVE_PROJECT_DRAFT:
                $action = $this->getModelManager()->getActionInstance("DraftProjectMetaDataAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_REMOVE_PROJECT_DRAFT:
                throw new NotAllowedPojectCommandException(ProjectKeys::KEY_REMOVE_PROJECT_DRAFT);

            case ProjectKeys::KEY_RENAME_PROJECT:
                throw new NotAllowedPojectCommandException(ProjectKeys::KEY_RENAME_PROJECT);

            default:
                throw new UnknownProjectException();
        }

        if (!$projectMetaData) throw new UnknownProjectException();
        return $projectMetaData;

    }

    protected function getDefaultResponse($response, &$ret) {}

    protected function _addExtraData(&$projectMetaData) {
        $rol = $this->authorization->getPermission()->getRol();
        if ($rol) {
            $projectMetaData[ProjectKeys::KEY_PROJECT_EXTRADATA][ProjectKeys::KEY_ROL] = $rol;
        }
        if ($projectMetaData[ProjectKeys::KEY_PROJECT_TYPE]) {
            $projectMetaData[ProjectKeys::KEY_PROJECT_EXTRADATA][ProjectKeys::KEY_PROJECT_TYPE] = $projectMetaData[ProjectKeys::KEY_PROJECT_TYPE];
        }else{
            $projectMetaData[ProjectKeys::KEY_PROJECT_EXTRADATA][ProjectKeys::KEY_PROJECT_TYPE] = $this->params[ProjectKeys::KEY_PROJECT_TYPE];
        }
    }
}
