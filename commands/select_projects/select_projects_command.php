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
        $this->types[AjaxKeys::KEY_DO] = self::T_STRING;
        $defaultValues = [AjaxKeys::KEY_ID => "",
                          AjaxKeys::KEY_DO => "select_projects"];
        $this->setParameters($defaultValues);
    }

    protected function process() {
        $action = $this->getModelManager()->getActionInstance("SelectProjectsAction");
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {}

    /**
     * @return string (nom del command per establir autoritzacions específiques)
     */
    public function getAuthorizationType() {
        return "_none";
    }

}
