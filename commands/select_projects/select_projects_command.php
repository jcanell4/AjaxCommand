<?php
/**
 * Class select_projects_command: Mostra una llista filtrada de projectes 
 * @author Rafael <rclaver@xtec.cat>
 */
if (!defined('DOKU_INC')) die();

class select_projects_command extends abstract_admin_command_class {

    public function __construct(){
        parent::__construct();
        $this->types[AjaxKeys::KEY_ID] = self::T_STRING;
        $this->setParameters([AjaxKeys::KEY_ID => "select_projects"]);
    }

    protected function process() {
        $action = $this->getModelManager()->getActionInstance("SelectProjectsAction");
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {
        if (isset($contentData['projectType'])) {
            $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo(
                    RequestParameterKeys::KEY_INFO,
                    WikiIocLangManager::getLang("list_projects_showed"),
                    $contentData[AjaxKeys::KEY_ID]
            ));
        }else {
            $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo(
                    RequestParameterKeys::KEY_INFO,
                    WikiIocLangManager::getLang("select_projects_loaded"),
                    $contentData[AjaxKeys::KEY_ID]
            ));
        }
    }

    /**
     * @return string (nom del command per establir autoritzacions específiques)
     */
    public function getAuthorizationType() {
        return "_none";
    }

}
