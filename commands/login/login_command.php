<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class login_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class login_command extends abstract_command_class {

    public function __construct() {

        parent::__construct();
        $this->authenticatedUsersOnly = FALSE;
        $this->types['do']            = abstract_command_class::T_STRING;
//        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['u'] = abstract_command_class::T_STRING;
//        $this->types['p'] = abstract_command_class::T_STRING;

        $defaultValues = array('do' => 'login');
        $this->setParameters($defaultValues);
    }

    /**
     * Comprova si els valors dels paràmetres emmagatzemats al objecte coincideixen amb 'do' i 'login', i l'usuari està
     * autenticat.
     *
     * Si els al array associatiu params existeix el valor 'do' i es 'login' es comprova si l'usuari esta authenticat i
     * s'emmagatzema aquest valor al array que es retoranarà.
     *
     * En cas de que el paràmetre 'do' no existeixi o no sigui login es procedeix a cridar al mètode privat _logoff per
     * tancar la sessió, i el valor emmagatzemat al array que es retornarà es fals.
     *
     * @return array associatium amb el valor del index loginResult cert o fals
     */
    protected function process() {
        $response = array(
            "loginRequest"  => $this->params['do'] === 'login'
            , "loginResult" => FALSE
        );

        if($this->params['do'] === 'login') {
            $response["loginResult"] = $this->isUserAuthenticated();
        } else if($this->isUserAuthenticated()) {
            $this->_logoff();
            $response["loginResult"] = FALSE;
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        $responseGenerator->addLoginInfo(
                          $response["loginRequest"],
                          $response["loginResult"]
        );
    }

    /**
     * Crida al mètode auth_logoff() de dokuwiki per tancar la sessió del usuari.
     */
    private function _logoff() {
        auth_logoff(TRUE);
    }
}