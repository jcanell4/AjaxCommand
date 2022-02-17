<?php
/**
 * Class selected_projects_command: Mostra una llista filtrada de projectes 
 * @author Rafael <rclaver@xtec.cat>
 */
if (!defined('DOKU_INC')) die();

class selected_projects_command extends abstract_admin_command_class {

    public function __construct(){
        parent::__construct();
    }

    protected function process() {
        if ($this->params[AjaxKeys::KEY_ID] !== "selected_projects") {
            $this->setParameters([AjaxKeys::KEY_ID => "selected_projects"]);
        }
        if ($this->params[AjaxKeys::KEY_DO] === "send_message") {
            $rols = $this->params['rols'];
            $missatge = $this->params['missatge'];
            $grups = $this->params['grups'];
        }
        $action = $this->getModelManager()->getActionInstance("SelectedProjectsAction");
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
