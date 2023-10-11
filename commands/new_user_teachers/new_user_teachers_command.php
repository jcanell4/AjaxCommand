<?php
/**
 * Class supplies_form_command: Mostra un formulari en una pàgina
 * @author Rafael <rclaver@xtec.cat>
 */
if (!defined('DOKU_INC')) die();

class new_user_teachers_command extends abstract_admin_command_class {

    public function __construct(){
        parent::__construct();
        $this->types[AjaxKeys::KEY_ID] = self::T_STRING;
        $this->setParameters([AjaxKeys::KEY_ID => "new_user_teachers"]);
    }

    protected function process() {
        if ($this->authorization->getPermission()->getInfoIsadmin()) {
            $action = $this->getModelManager()->getActionInstance("NewUserTeachersAction");
            $response = $action->get($this->params);
        }else {
            throw new Exception("No té permís per a aquest element.");
        }
        return $response;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo(
                                                RequestParameterKeys::KEY_INFO,
                                                WikiIocLangManager::getLang("new_user_teachers_loaded"),
                                                $contentData[AjaxKeys::KEY_ID]
                                        ));
    }

    /*
     * @return string (nom del command per establir autoritzacions específiques)
     */
    public function getAuthorizationType() {
        /*
         * JOSEP: Caldrà definir les autoritzacions! Queda pendent.
         */
        return "_none";
    }

}
