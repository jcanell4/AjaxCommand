<?php
if (!defined('DOKU_INC')) die();

require_once(DOKU_COMMAND . "defkeys/UserStateKeys.php");

/**
 * Class login_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class login_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AjaxKeys::KEY_DO] = self::T_STRING;
        $this->types['unlock'] = self::T_ARRAY;
        $this->types['u'] = self::T_STRING;

        $this->setParameters([AjaxKeys::KEY_DO => 'login']);
    }

    public function init($modelManager = NULL) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
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
    protected function process(){

        if ($this->params[AjaxKeys::KEY_DO] === 'relogin') {
            $response = $this->processCheck();
        } else {
            $response = $this->processLogin(); // ALERTA[Xavi] Aquesta funció conté el codi de login original sense modificar (inclou el logout);
        }

        if (!$response["loginResult"] || !$response["loginRequest"]) {
            $notifications = $this->_getContentNotifyAction("close");
            $response = array_merge($response, $notifications);
        }

        return $response;
    }

    private function processCheck() {

        $response = array(
            "loginRequest" => true,
            "loginResult" => $this->authorization->isUserAuthenticated($this->params['userId'])
        );

        if ($response["loginResult"]) {
            $response["userId"] = $this->params['userId'];
            $notifications = $this->_getContentNotifyAction("init");
            $response = array_merge($response, $notifications);
            $response['user_state'] = $this->getUserConfig($this->params['userId']);
        }
        return $response;
    }

    private function processLogin() {
        $response = array(
            "loginRequest" => ($this->params[AjaxKeys::KEY_DO] === 'login'),
            "loginResult" => $this->authorization->isUserAuthenticated()
        );

        if ($response["loginRequest"] && $response["loginResult"]) {
            $response["userId"] = $this->params['u'];
            $notifications = $this->_getContentNotifyAction("init");
            $response = array_merge($response, $notifications);
            $response['user_state'] = $this->getUserConfig($this->params['u']);

        } else if ($response["loginResult"]) {
            $this->_logoff();
            $response["loginResult"] = FALSE;
            $notifications = $this->_getContentNotifyAction("close");
            $response = array_merge($response, $notifications);
        }
        return $response;
    }

    function getUserConfig($user) {
        // Carregar fitxer amb la configuració
//        $dir = WikiGlobalConfig::getConf("userdatadir"); // TODO[Xavi]: Afegit el directori al ownInit/init.php
        $dir = fullpath(DOKU_INC . '/data/user_state');

        //$filename = $dir . '/' . md5(cleanID($user)) . '.config'; // TODO[Xavi]: deixem el nom de fitxer hashejat o en textpla?
        $filename = $dir . '/' . cleanID($user) . '.config';

        if (@file_exists($filename)) {
            $config = json_decode(io_readFile($filename, false), true);
        } else {
            // PROVISIONAL[Xavi] si no existeix el fitxer es crea un amb la configuració per defecta: editor ACE
            $config = [
                'editor' => UserStateKeys::KEY_ACE
//                'editor' => UserStateKeys::KEY_DOJO
            ];
            io_saveFile($filename, json_encode($config));
        }

        return $config;
    }

    /**
     * Afegeix una resposta de tipus LOGIN_INFO al generador de respostes a partir de la informació passada com argument.
     * @param array $response array associatiu amb els valors de 'loginRequest' i 'loginResult'
     * @param AjaxCmdResponseGenerator $responseGenerator objecte al que s'afegirà la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$responseGenerator) {
        $responseGenerator->addLoginInfo(
            $response["loginRequest"],
            $response["loginResult"],
            $response["userId"]
        );
    }

    /**
     * Crida al mètode auth_logoff() de dokuwiki per tancar la sessió del usuari.
     */
    private function _logoff() {
        $this->getModelAdapter()->logoff();
    }

    private function _getContentNotifyAction($do) {
        $action = $this->getModelManager()->getActionInstance("NotifyAction", FALSE);
        $params = array(AjaxKeys::KEY_DO => $do);
        $content = $action->get($params, FALSE);
        return $content;
    }
}
