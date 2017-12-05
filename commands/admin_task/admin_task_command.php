<?php
if(!defined('DOKU_INC')) die();
/**
 * Class admin_task_command
 * @author Eduard Latorre
*/
class admin_task_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AjaxKeys::KEY_DO] = self::T_STRING;
        $this->setPermissionFor(['admin', 'manager']);
        $this->setParameters([AjaxKeys::KEY_DO => 'admin']);
    }

    /**
    * Retorna la pàgina corresponent a la tasca d'administració 'page'.
    * @return array amb la informació de la pàgina formatada amb 'id', 'tittle' i 'content'
    */
    protected function process() {
        $contentData = $this->modelWrapper->getAdminTask($this->params);
        return $contentData;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {}
}
