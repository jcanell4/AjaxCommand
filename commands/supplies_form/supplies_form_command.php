<?php
/**
 * Class supplies_form_command: Mostra un formulari en una pàgina
 * @author Rafael <rclaver@xtec.cat>
 */
if (!defined('DOKU_INC')) die();

class supplies_form_command extends abstract_admin_command_class {

    public function __construct(){
        parent::__construct();
        $this->types[AjaxKeys::KEY_ID] = self::T_STRING;
        $this->setParameters([AjaxKeys::KEY_ID => "supplies_form"]);
    }

    protected function process() {
        $action = $this->getModelManager()->getActionInstance("SuppliesFormAction");
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo(
                                                RequestParameterKeys::KEY_INFO,
                                                WikiIocLangManager::getLang("supplies_form_loaded"),
                                                $contentData[AjaxKeys::KEY_ID]
                                        ));
    }

    // @return string (nom del command per establir autoritzacions específiques)
    public function getAuthorizationType() {
        /*
         * JOSEP: Caldrà definir les autoritzacions! Queda pendent.
         */ 
        return "_none";
    }

}
