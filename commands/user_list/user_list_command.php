<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");

require_once(DOKU_COMMAND . 'defkeys/PageKeys.php');
require_once(DOKU_PLUGIN . 'wikiiocmodel/actions/UserListAction.php');

/**
 * Class user_list_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class user_list_command extends abstract_command_class {
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;

        $this->types[AjaxKeys::KEY_ID] = self::T_STRING;
        $this->types[AjaxKeys::KEY_DO] = self::T_STRING;
        $this->types[ProjectKeys::KEY_FILTER] = self::T_STRING;
        $this->types[PageKeys::KEY_START_POS] = self::T_INTEGER;
        $this->types[PageKeys::KEY_PROJECT] = self::T_STRING;
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        $responseGenerator->addArrayTypeResponse($response);
    }

    protected function process() {
        if (!isset($this->params[AjaxKeys::KEY_DO])) {
            if (isset($this->params[PageKeys::KEY_PROJECT]) && isset($this->params[AjaxKeys::KEY_ID])){
                $this->params[AjaxKeys::KEY_DO] = UserListAction::OF_A_PROJECT;
            }elseif(isset($this->params[AjaxKeys::KEY_ID])){
                $this->params[AjaxKeys::KEY_DO] = UserListAction::BY_PAGE_PERMSION;
            }else{
                $this->params[AjaxKeys::KEY_DO] = UserListAction::BY_NAME;
            }
        }

        $action = new UserListAction($this->modelAdapter->getPersistenceEngine());
        $response = $action->get($this->params);
        return $response;
    }
}
