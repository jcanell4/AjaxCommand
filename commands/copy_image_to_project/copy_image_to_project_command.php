<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class copy_image_to_project_command
 *
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
class copy_image_to_project_command extends abstract_command_class {
    //(0=OK, -1=UNAUTHORIZED, -2=OVER_WRITING_NOT_ALLOWED, 
    //-3=OVER_WRITING_UNAUTHORIZED, -5=FAILS, -4=WRONG_PARAMS
    //-6=BAD_CONTENT, -7=SPAM_CONTENT, -8=XSS_CONTENT)

    /**
     * Codi d'informació que indica que el fitxer pot contenir contingut maligne
     *
     * @return integer Retorna un -8
     */
    private static $XSS_CONTENT = -8;

    /**
     * Codi d'informació que indica que el fitxer pot contenir spam
     *
     * @return integer Retorna un -7
     */
    private static $SPAM_CONTENT = -7;

    /**
     * Codi d'informació que indica que el contingut no es correspon amb el
     * tipus declarat.
     *
     * @return integer Retorna un -6
     */
    private static $BAD_CONTENT = -6;

    /**
     * Codi d'informació degut a que els paràmetres han agafat valors incorrectes.
     *
     * @return integer Retorna un -4
     */
    private static $WRONG_PARAMS = -4;

    /**
     * Codi d'informació per quan no s'han pogut copiar totes les imatges.
     *
     * @return integer Retorna un -5
     */
    private static $COPY_FAILS = -5;

    /**
     * Codi d'informació per quan s'intenta sobrescriure, però no es permet
     *
     * @return integer Retorna un -3
     */
    private static $OVER_WRITING_UNAUTHORIZED = -3;

    /**
     * Codi d'informació per quan s'intenta sobrescriure, però no es permet
     *
     * @return integer Retorna un -2
     */
    private static $OVER_WRITING_NOT_ALLOWED = -2;

    /**
     * Codi d'informació per quan s'han copiat totes les imatges.
     *
     * @return integer Retorna un -1
     */
    private static $UNAUTHORIZED_USER = -1;

    /**
     * Codi d'informació per quan s'han copiat totes les imatges.
     *
     * @return integer Retorna un 0
     */
    private static $OK = 0;

    /**
     * Codi d'informació per quan no arriba el directori del projecte.
     *
     * @return integer Retorna un -10
     */
    private static $UNDEFINED_PROJECT_CODE = -10;

    private static $PROJECT_PATH_PARAM = "projectPath";
    private static $CHECK_IMAGE_PARAM = "checkImage";
    private static $IMAGE_NAME_PARAM = "imageName";
    private static $DOT = ".";

    /**
     * El constructor estableix que no es necessari estar autenticat si existeix el fitxer debug, o que cal estar-ho
     * en cas contrari.
     */
    public function __construct() {
        parent::__construct();
        if(@file_exists(DOKU_INC . 'debug')) {
            $this->authenticatedUsersOnly = FALSE;
        } else {
            $this->authenticatedUsersOnly = TRUE;
        }
        //TODO[Xavi] $defaultValues no es definit en aquest scope, d'on s'ha de treure el valor per defecte?
        $this->setParameters($defaultValues);
    }

    /**
     * Guarda la imatge i retorna el codi obtingut.
     *
     * @return int codi del resultat obtingut al intentar guardar la imatge
     */
    protected function process() {
        $response = self::$UNDEFINED_PROJECT_CODE;
        if(array_key_exists(self::$PROJECT_PATH_PARAM, $this->params)) {
            $imagesPath = $this->getImageRepositoryDir();
            $filename   = $this->params[self::$CHECK_IMAGE_PARAM];
            $imageName  = $this->params[self::$IMAGE_NAME_PARAM];
            $ext        = strrchr($filename, self::$DOT);
            $response   = $this->modelWrapper->saveImage(
                                             $this->params[self::$PROJECT_PATH_PARAM],
                                             $imageName . $ext,
                                             $imagesPath . $filename,
                                             TRUE
            );
        }
        return $response;
    }

    /**
     * Afegeix una resposta de tipus CODE_TYPE_RESPONSE al generador de respostes passat com argument.
     *
     * @param integer                  $response
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     *
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        //$lang['uploadwrong']
        //$lang['uploadexist']
        //$lang['uploadbadcontent']
        //$lang['uploadspam']
        //$lang['uploadxss']
        //$lang['uploadfail']
        //$lang['uploadsucc']
        $responseCode = $response;
        $info         = "";
        switch($responseCode) {
            case self::$BAD_CONTENT:
                $info = $this->modelWrapper->getGlobalMessage('uploadbadcontent');
                break;
            case self::$COPY_FAILS:
                $info = $this->modelWrapper->getGlobalMessage('uploadfail');
                break;
            case self::$OK:
                $info = $this->modelWrapper->getGlobalMessage('uploadsucc');
                break;
            case self::$OVER_WRITING_UNAUTHORIZED:
                $info = $this->getLang('unauthorized_request');
                break;
            case self::$OVER_WRITING_NOT_ALLOWED:
                $info = $this->modelWrapper->getGlobalMessage('uploadexist');
                break;
            case self::$SPAM_CONTENT:
                $info = $this->modelWrapper->getGlobalMessage('uploadspam');
                break;
            case self::$UNAUTHORIZED_USER:
                $info = $this->getLang('unauthorized_request');
                break;
            case self::$WRONG_PARAMS:
                $info = $this->modelWrapper->getGlobalMessage('uploadwrong');
                break;
            case self::$XSS_CONTENT:
                $info = $this->modelWrapper->getGlobalMessage('uploadxss');
                break;
            case self::$UNDEFINED_PROJECT_CODE:
                $info = $this->getLang('undefinedProject');
                break;
            default:
                $info = $this->getLang('unexpectedError');
                break;
        }
        $ret->addCodeTypeResponse($responseCode, $info);
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     *
     * @global array $conf
     * @return string Retorna el directori definit per les imatges de processing.
     */
    private function getImageRepositoryDir() {
        global $conf;
        return $conf['mediadir'] . '/' . $this->getConf('processingImageRepository');
    }
}