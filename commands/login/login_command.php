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

    /**
     * El constructor extableix que no es necessari estar autenticat, el tipus,
     * els valors per defecte i els estableix
     * com a paràmetres.
     * El valor per defecte es el paràmetre 'do' amb valor 'login'.
     */
    public function __construct() {
        parent::__construct();
        $this->types['do'] = abstract_command_class::T_STRING;
        $this->types['unlock'] = abstract_command_class::T_ARRAY;
        $this->types['u'] = abstract_command_class::T_STRING;

        $defaultValues = array('do' => 'login');
        $this->setParameters($defaultValues);
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


        if ($this->params[PageKeys::KEY_DO] === 'relogin') {
            $response = $this->processCheck();
        } else {
            $response = $this->processLogin(); // ALERTA[Xavi] Aquesta funció conté el codi de login original sense modificar (inclou el logout);
        }

        if (!$response["loginResult"] || !$response["loginRequest"]) {
            $response = array_merge($response, $this->modelWrapper->notify(['do' => 'close']));
        }

        return $response;
    }

    private function processCheck()
    {


        $response = array(
//            "loginRequest" => $this->authorization->isUserAuthenticated($this->params['userId']),
//            "loginResult" => $this->authorization->isUserAuthenticated()

            "loginRequest" => true,
            "loginResult" => $this->authorization->isUserAuthenticated($this->params['userId'])
        );

        if ($response["loginResult"] /*&& $response["loginRequest"]*/) {
            $response["userId"] = $this->params['userId'];

            $notifications = $this->modelWrapper->notify(['do' => 'init']);
            $response = array_merge($response, $notifications);
            $response['user_state'] = $this->getUserConfig($this->params['userId']);


        } else if ($response["loginResult"]) {
            $this->_logoff();
            $response["loginResult"] = FALSE;
        }



        return $response;

    }

    private function processLogin()
    {
        $response = array(
             "loginRequest"  => $this->params['do'] === 'login'
            ,"loginResult" => $this->authorization->isUserAuthenticated()
        );

        if($this->params['do'] === 'login'  && $response["loginResult"] ) {
            $response["userId"] = $this->params['u'];

            $notifications = $this->modelWrapper->notify(['do' => 'init']);
            $response = array_merge($response, $notifications);
            $response['user_state'] = $this->getUserConfig($this->params['u']);


        } else if ($response["loginResult"]) {
            $this->_logoff();
            $response["loginResult"] = FALSE;
            $response = array_merge($response, $this->modelWrapper->notify(['do' => 'close']));
        }





        return $response;
    }

    function getUserConfig($user) {
        // Carregar fitxer amb la configuració
//        $dir = WikiGlobalConfig::getConf("userdatadir"); // TODO[Xavi]: Afegit el directori al ownInit/init.php
        $dir = fullpath(DOKU_INC .'/data/user_state');

//        $filename = $dir . '/' . md5(cleanID($user)) . '.config'; // TODO[Xavi]: deixem el nom de fitxer hashejat o en textpla?
        $filename = $dir . '/' . cleanID($user) . '.config';

        if (@file_exists($filename)) {
            $config = json_decode(io_readFile($filename, false), t);
        } else {
            // PROVISIONAL[Xavi] si no existeix el fitxer es crea un amb la configuració per defecta: editor ACE
            $config = ['editor' => 'ACE'];
//            $config = ['editor' => 'Dojo'];
            io_saveFile($filename, json_encode($config));
        }

        return $config;
    }

    /**
     * Afegeix una resposta de tipus LOGIN_INFO al generador de respostes a partir de la informació passada com argument.
     *
     * @param array $response array associatiu amb els valors de 'loginRequest' i 'loginResult'
     * @param AjaxCmdResponseGenerator $responseGenerator objecte al que s'afegirà la resposta
     *
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
        $this->getModelWrapper()->logoff();
    }
}