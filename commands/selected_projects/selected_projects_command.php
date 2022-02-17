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
            $this->setParameters(['type' => "warning",
                                  'to' => "",
                                  'send_email' => true
                                 ]);
            $action = $this->getModelManager()->getActionInstance("SendMessageAction");
            $response = $action->get($this->params);
        }else {
            $action = $this->getModelManager()->getActionInstance("SelectedProjectsAction");
            $response = $action->get($this->params);
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        if ($this->params[AjaxKeys::KEY_DO] === "send_message") {
            if (isset($response['info'])) {
                $responseGenerator->addInfoDta($response['info']);
            }
            foreach ($response['notifications'] as $notification) {
                $responseGenerator->addNotificationResponse($notification['action'], $notification['params']);
            }
        }else {
            $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo(
                                            RequestParameterKeys::KEY_INFO,
                                            WikiIocLangManager::getLang("list_projects_showed"),
                                            $response[AjaxKeys::KEY_ID]
                                        ));
        }
    }

    //@return string (nom del command per establir autoritzacions específiques)
    public function getAuthorizationType() {
        /*
         * JOSEP: Caldrà definir les autoritzacions! Queda pendent.
         */ 
        return "_none";
    }

}
