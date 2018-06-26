<?php
/**
 * Class recent_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();
require_once DOKU_INC."lib/lib_ioc/ajaxcommand/abstract_command_class.php";

class recent_command extends abstract_command_class {

    public function __construct(){
        parent::__construct();
        $this->types[AjaxKeys::KEY_ID] = self::T_STRING;
        $this->types[RequestParameterKeys::SHOW_CHANGES_KEY] = self::T_STRING;
        $this->types[RequestParameterKeys::FIRST_KEY] = self::T_ARRAY_KEY;

        $defaultValues = [
            AjaxKeys::KEY_ID => '',
            RequestParameterKeys::SHOW_CHANGES_KEY => 'both',
            RequestParameterKeys::FIRST_KEY => array(0 => ''),
        ];
        $this->setParameters($defaultValues);
    }

    protected function process() {
        $action = $this->getModelManager()->getActionInstance("RecentListAction");
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {}

    /**
     * @return string (nom del command, a partir del nom de la clase,
     *                 modificat pels valors de $params per definir subclasses específiques
     *                 amb autoritzacions específiques)
     */
    public function getAuthorizationType() {
        return "_none";
    }

}
