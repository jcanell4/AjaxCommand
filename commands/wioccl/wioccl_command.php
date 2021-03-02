<?php
/**
 * Class user_list_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class wioccl_command extends abstract_command_class {
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;

        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_DO] = self::T_STRING;
        $this->types[PageKeys::KEY_FILTER] = self::T_STRING;
        $this->types[PageKeys::KEY_START_POS] = self::T_INTEGER;
        $this->types[PageKeys::KEY_PROJECT] = self::T_STRING;
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        $responseGenerator->addObjectTypeResponse($response);
    }

    protected function process() {
//        if (!isset($this->params[AjaxKeys::KEY_DO])) {
//            if (isset($this->params[PageKeys::KEY_ID])) {
//                if (isset($this->params[PageKeys::KEY_PROJECT])) {
//                    $this->params[PageKeys::KEY_DO] = UserListAction::OF_A_PROJECT;
//                }elseif(isset($this->params[PageKeys::KEY_FILTER])) {
//                    $this->params[PageKeys::KEY_DO] = UserListAction::BY_NAME;
//                }else {
//                    $this->params[PageKeys::KEY_DO] = UserListAction::BY_PAGE_PERMSION;
//                }
//            }else {
//                throw new IncorrectParametersException();
//            }
//        }

        $action = $this->getModelManager()->getActionInstance("WiocclAction");
        $response = $action->get($this->params);
        return $response;
    }

    public function getAuthorizationType()
    {
        // TODO[Xavi] Cal revisar la autenticació d'aquesta comanda, la hem deixat com a 'page' perquè pot
        // haver casos en que es vulgui fer una traducció wioccl-html sense estar dintre d'un projecte
        // però en aquest cas tampoc no funiconaría perquè actualment es necessita un projecte per obtenir
        // el datasource.
        // ALERTA! si es posa '_none' peta amb error 500.
        return 'page';
//        return parent::getAuthorizationType(); // TODO: Change the autogenerated stub
    }
}
