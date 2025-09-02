<?php
/**
 * Class login_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

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

        if (!$response[ProjectKeys::KEY_LOGIN_RESULT] || !$response[ProjectKeys::KEY_LOGIN_REQUEST]) {
            $notifications = $this->_getContentNotifyAction("close");
            $response = array_merge($response, $notifications);
        }

        return $response;
    }

    private function processCheck() {
        $hasMoodleToken = !empty($this->params[ProjectKeys::KEY_MOODLE_TOKEN]);
        $userinfo = WikiIocInfoManager::getInfo('userinfo');
        $isUserMoodle = (isset($userinfo['moodle'])) ? $userinfo['moodle'] : false;
        $response = array(
            ProjectKeys::KEY_LOGIN_REQUEST => true,
            ProjectKeys::KEY_LOGIN_RESULT => $this->authorization->isUserAuthenticated($this->params[ProjectKeys::KEY_USER_ID])
        );

        if ($response[ProjectKeys::KEY_LOGIN_RESULT]) {
            $response[ProjectKeys::KEY_USER_ID] = $this->params[ProjectKeys::KEY_USER_ID];
            $response[ProjectKeys::KEY_MOODLE_TOKEN] = $this->getMoodleToken();
            $notifications = $this->_getContentNotifyAction("init");
            $response = array_merge($response, $notifications);
            $response[ProjectKeys::KEY_USER_STATE] = $this->getUserConfig();

            if ($hasMoodleToken) {
                //Refresca la sessió de moodle donat que el timer del client es posarà a 0 amb aquest relogin
                $response[ProjectKeys::KEY_MOODLE_TOKEN] = $this->params[ProjectKeys::KEY_MOODLE_TOKEN];
                $action = $this->getModelManager()->getActionInstance("RefreshMoodleSessionAction", FALSE);
                $action->get($this->params);
            }elseif ($isUserMoodle) {
                $this->_logoff();
                unset($response[ProjectKeys::KEY_USER_STATE]);
                $response[ProjectKeys::KEY_LOGIN_REQUEST] = FALSE;
                $response[ProjectKeys::KEY_LOGIN_RESULT] = FALSE;
                $notifications = $this->_getContentNotifyAction("close");
                $response = array_merge($response, $notifications);
            }
        }
        return $response;
    }

    private function processLogin() {
        $response = array(
            ProjectKeys::KEY_LOGIN_REQUEST => ($this->params[AjaxKeys::KEY_DO] === 'login'),
            ProjectKeys::KEY_LOGIN_RESULT => $this->authorization->isUserAuthenticated()
        );

        if ($response[ProjectKeys::KEY_LOGIN_REQUEST] && $response[ProjectKeys::KEY_LOGIN_RESULT]) {
            $response[ProjectKeys::KEY_USER_ID] = $this->params['u'];
            $response[ProjectKeys::KEY_MOODLE_TOKEN] = $this->getMoodleToken();
            $notifications = $this->_getContentNotifyAction("init");
            $response = array_merge($response, $notifications);
            $response[ProjectKeys::KEY_USER_STATE] = $this->getUserConfig();

        }else if ($response[ProjectKeys::KEY_LOGIN_RESULT]) {
            $this->_logoff();
            $response[ProjectKeys::KEY_LOGIN_RESULT] = FALSE;
            $notifications = $this->_getContentNotifyAction("close");
            $response = array_merge($response, $notifications);
        }
        return $response;
    }

    function getUserConfig() {
        $config = array();
        $config['editor'] = WikiIocInfoManager::getInfo("userinfo")['editor'];
        return $config;
    }

    private function getMoodleToken() {
        if ($this->moodleToken("has")){
            $moodleToken = $this->moodleToken("get");
        }elseif($this->params[ProjectKeys::KEY_MOODLE_TOKEN]) {
            $moodleToken = $this->params[ProjectKeys::KEY_MOODLE_TOKEN];
        }
        return $moodleToken;
    }

    private function moodleToken($act="has"){
        global $auth;
        if($act==="get"){
            $ret="";
            if(is_callable(array($auth, "getMoodleToken"))){
                $ret =  $auth->getMoodleToken();
            }
        }else{
            $ret = FALSE;
            if(is_callable(array($auth, "hasMoodleToken"))){
                $ret = $auth->hasMoodleToken();
            }
        }
        return $ret;
    }

    /**
     * Afegeix una resposta de tipus LOGIN_INFO al generador de respostes a partir de la informació passada com argument.
     * @param array $response array associatiu amb els valors de 'loginRequest' i 'loginResult'
     * @param AjaxCmdResponseGenerator $responseGenerator objecte al que s'afegirà la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$responseGenerator) {
        $responseGenerator->addLoginInfo(
            $response[ProjectKeys::KEY_LOGIN_REQUEST],
            $response[ProjectKeys::KEY_LOGIN_RESULT],
            $response[ProjectKeys::KEY_USER_ID],
            $response[ProjectKeys::KEY_MOODLE_TOKEN]
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
        $content = $action->get($params);
        return $content;
    }
}
