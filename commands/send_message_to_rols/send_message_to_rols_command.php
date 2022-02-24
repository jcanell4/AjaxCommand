<?php
/**
 * send_message_to_rols_command: Envia missatges als usuaris de projectes seleccionats per rol
 * @author Rafael <rclaver@xtec.cat>
 */
if (!defined('DOKU_INC')) die();

class send_message_to_rols_command extends abstract_admin_command_class {

    public function __construct(){
        parent::__construct();
    }

    protected function process() {
        $this->setParameters(['type' => "warning",
                              'to' => "",
                              'send_email' => true
                             ]);
        $action = $this->getModelManager()->getActionInstance("SendMessageToRolsAction");
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        if (isset($response['info'])) {
            $responseGenerator->addInfoDta($response['info']);
        }
        if (isset($response['notifications'])) {
            foreach ($response['notifications'] as $notification) {
                $responseGenerator->addNotificationResponse($notification['action'], $notification['params']);
            }
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
