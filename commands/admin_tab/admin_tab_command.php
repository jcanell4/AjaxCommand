<?php
/**
 * [Rafa] Me sabe grave pero parece ser que este comando es un pobre huerfanito al que nadie llama
 */
if(!defined('DOKU_INC')) die();
/**
 * Class admin_tab_command: crea la pestanya admin
 * @author Eduardo Latorre Jarque <eduardo.latorre@gmail.com>
*/
class admin_tab_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AjaxKeys::KEY_DO] = self::T_STRING;
        $this->setPermissionFor(array('admin','manager'));
        $this->setParameters([AjaxKeys::KEY_DO => 'admin']);
    }

    /**
    * Retorna la informació de la pestanya admin
    * @return array amb la informació de la pàgina formatada amb 'id', 'tittle' i 'content'
    */
    protected function process() {
//        $contentData = $this->modelWrapper->getAdminTaskList();
//        return $contentData;
        $action = $this->modelManager->getActionInstance("AdminTaskListAction");
        $content = $action->get();
        return $content;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {}

    public function getAuthorizationType() {
        return '_none';
    }
}
