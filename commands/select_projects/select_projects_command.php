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
        $this->setParameters([AjaxKeys::KEY_ACTION_COMMAND => "select_projects"]);
    }

    protected function process() {
        $this->setParameters([AjaxKeys::KEY_ID => "select_projects"]);
        $action = $this->getModelManager()->getActionInstance("SelectProjectsAction");
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo(
                                            RequestParameterKeys::KEY_INFO,
                                            WikiIocLangManager::getLang("list_projects_showed"),
                                            $contentData[AjaxKeys::KEY_ID]
                                        ));
    }

    //@return string (nom del command per establir autoritzacions específiques)
    public function getAuthorizationType() {
        /*
         * JOSEP: Caldrà definir les autoritzacions! Queda pendent.
         */ 
        return "_none";
    }

}
