<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN.'ajaxcommand/defkeys/AdminKeys.php');
/**
 * Class admin_task_command
 * @author Eduard Latorre
*/
class admin_task_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AdminKeys::KEY_DO] = self::T_STRING;
        $this->setPermissionFor(['admin', 'manager']);
        $this->setParameters([AdminKeys::KEY_DO => 'admin']);
    }

    /**
    * Retorna la pàgina corresponent a la tasca d'administració 'page'.
    * @return array amb la informació de la pàgina formatada amb 'id', 'tittle' i 'content'
    */
    protected function process() {
        $params = array(AdminKeys::KEY_DO   => $this->params[AdminKeys::KEY_DO],
                        AdminKeys::KEY_PAGE => $this->params[AdminKeys::KEY_PAGE]
                  );
        $action = $this->getModelManager()->getActionInstance("AdminTaskAction");
        $content = $action->get($params);
        return $content;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {}
}
