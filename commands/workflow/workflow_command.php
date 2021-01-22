<?php
/**
 * Class workflow_command: Llama a las Actions correspondientes por cada petición recibida en &action=
 * @culpable Rafael Claver <rclaver@xtec.cat>
 */
if (!defined('DOKU_INC')) die();

class workflow_command extends abstract_project_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[ProjectKeys::KEY_CANCEL] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_CLOSE] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_REFRESH] = self::T_BOOLEAN;
        $this->types[ProjectKeys::KEY_HAS_DRAFT] = self::T_BOOLEAN;
    }

    protected function process() {
        if (!$this->params[ProjectKeys::KEY_PROJECT_TYPE])
            throw new UnknownPojectTypeException();

        switch ($this->params[ProjectKeys::KEY_ACTION]) {

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

            case ProjectKeys::KEY_CREATE_PROJECT:
                $action = $this->getModelManager()->getActionInstance("CreateProjectMetaDataAction");
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

    public function getAuthorizationType() {
//        $pModel = $this->getModelManager();
//        $pMetaDataQuery = $this->persistenceEngine->createProjectMetaDataQuery();
        return $this->params[ProjectKeys::KEY_DO];
    }

}
