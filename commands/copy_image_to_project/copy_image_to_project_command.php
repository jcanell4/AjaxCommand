<?php
if(!defined('DOKU_INC')) die();
/**
 * Class copy_image_to_project_command
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
class copy_image_to_project_command extends abstract_command_class {

    const XSS_CONTENT = -8;   //indica que el fitxer pot contenir contingut maligne
    const SPAM_CONTENT = -7;  //indica que el fitxer pot contenir spam
    const BAD_CONTENT = -6;   //indica que el contingut no es correspon amb el tipus declarat
    const WRONG_PARAMS = -4;  //indica que els paràmetres han agafat valors incorrectes
    const COPY_FAILS = -5;    //indica que no s'han pogut copiar totes les imatges
    const OVER_WRITING_UNAUTHORIZED = -3; //indica quan s'intenta sobrescriure, però no es permet
    const OVER_WRITING_NOT_ALLOWED = -2;  //indica quan s'intenta sobrescriure, però no es permet
    const UNAUTHORIZED_USER = -1;         //indica usuari no autoritzat
    const OK = 0;             //indica que s'han copiat totes les imatges
    const UNDEFINED_PROJECT_CODE = -10;   //indica quan no arriba el directori del projecte

    const PROJECT_PATH_PARAM = "projectPath";
    const CHECK_IMAGE_PARAM = "checkImage";
    const IMAGE_NAME_PARAM = "imageName";
    const DOT = ".";

    /**
     * El constructor estableix que no es necessari estar autenticat si existeix el fitxer debug, o que cal estar-ho
     * en cas contrari.
     */
    public function __construct() {
        parent::__construct();
        if (@file_exists(DOKU_INC . 'debug')) {
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
        $response = self::UNDEFINED_PROJECT_CODE;
        if(array_key_exists(self::PROJECT_PATH_PARAM, $this->params)) {
            $imagesPath = $this->getImageRepositoryDir();
            $filename   = $this->params[self::CHECK_IMAGE_PARAM];
            $imageName  = $this->params[self::IMAGE_NAME_PARAM];
            $ext        = strrchr($filename, self::DOT);
            $response   = $this->modelWrapper->saveImage(
                                             $this->params[self::PROJECT_PATH_PARAM],
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
            case self::BAD_CONTENT:
                $info = WikiIocLangManager::getLang('uploadbadcontent');
                break;
            case self::COPY_FAILS:
                $info = WikiIocLangManager::getLang('uploadfail');
                break;
            case self::OK:
                $info = WikiIocLangManager::getLang('uploadsucc');
                break;
            case self::OVER_WRITING_UNAUTHORIZED:
                $info = $this->getLang('unauthorized_request');
                break;
            case self::OVER_WRITING_NOT_ALLOWED:
                $info = WikiIocLangManager::getLang('uploadexist');
                break;
            case self::SPAM_CONTENT:
                $info = WikiIocLangManager::getLang('uploadspam');
                break;
            case self::UNAUTHORIZED_USER:
                $info = $this->getLang('unauthorized_request');
                break;
            case self::WRONG_PARAMS:
                $info = WikiIocLangManager::getLang('uploadwrong');
                break;
            case self::XSS_CONTENT:
                $info = WikiIocLangManager::getLang('uploadxss');
                break;
            case self::UNDEFINED_PROJECT_CODE:
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