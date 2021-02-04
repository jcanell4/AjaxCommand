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
                $action = $this->getModelManager()->getActionInstance("DiffProjectAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_VIEW:
                $action = $this->getModelManager()->getActionInstance("ViewProjectAction");
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
                    $action = $this->getModelManager()->getActionInstance("GetProjectAction");
                }
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_SAVE:
                $action = $this->getModelManager()->getActionInstance("SetProjectAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_CREATE_PROJECT:
                $action = $this->getModelManager()->getActionInstance("CreateProjectAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_CREATE_SUBPROJECT:
                //Está en: lib/plugins/ajaxcommand/commands/create_subproject/create_subproject_command.php
                break;

            case ProjectKeys::KEY_GENERATE:
                $action = $this->getModelManager()->getActionInstance("GenerateProjectAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_CANCEL:
                $action = $this->getModelManager()->getActionInstance("CancelProjectAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_REVERT_PROJECT:
                //También està, como ejemplo, en: <en_algún_plugin>/projects/documentation/command/projectRevert.php
                $action = $this->getModelManager()->getActionInstance("RevertProjectAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_SAVE_PROJECT_DRAFT:
                $action = $this->getModelManager()->getActionInstance("DraftProjectAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_REMOVE_PROJECT_DRAFT:
                throw new NotAllowedPojectCommandException(ProjectKeys::KEY_REMOVE_PROJECT_DRAFT);

            case ProjectKeys::KEY_RENAME_PROJECT:
                $action = $this->getModelManager()->getActionInstance("RenameProjectAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData); //Potser millor posar-ho al postResponse del command i del ResponseHandler que és on s'afegeixen altres extra datas.
                break;

            case ProjectKeys::KEY_DUPLICATE_PROJECT:
                $action = $this->getModelManager()->getActionInstance("DuplicateProjectAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            case ProjectKeys::KEY_REMOVE_PROJECT:
                $action = $this->getModelManager()->getActionInstance("RemoveProjectAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_FTP_PROJECT:
                $action = $this->getModelManager()->getActionInstance("FtpProjectAction");
                $projectMetaData = $action->get($this->params);
                break;

            case ProjectKeys::KEY_WORKFLOW:
                $action = $this->getModelManager()->getActionInstance("WorkflowProjectAction");
                $projectMetaData = $action->get($this->params);
                $this->_addExtraData($projectMetaData);
                break;

            default:
                throw new UnknownProjectException($this->params[ProjectKeys::KEY_ID]);
        }

        if (!$projectMetaData)
            throw new UnknownProjectException($this->params[ProjectKeys::KEY_ID]);

        return $projectMetaData;
    }

    protected function getDefaultResponse($response, &$ret) {}

}
