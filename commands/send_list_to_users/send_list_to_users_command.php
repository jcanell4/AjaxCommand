<?php
/**
 * send_list_to_users_command: Envia missatges als usuaris seleccionats
 * @author Rafael <rclaver@xtec.cat>
 */
if (!defined('DOKU_INC')) die();

class send_list_to_users_command extends abstract_admin_command_class {

    public function __construct(){
        parent::__construct();
    }

    protected function process() {
        $action = $this->getModelManager()->getActionInstance("SendListToUsersAction");
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        if (isset($response['info'])) {
            $responseGenerator->addInfoDta($response['info']);
        }
        foreach ($response['notifications'] as $notification) {
            $responseGenerator->addNotificationResponse($notification['action'], $notification['params']);
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
