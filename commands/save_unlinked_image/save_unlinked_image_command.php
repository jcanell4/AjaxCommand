<?php
if (!defined('DOKU_INC')) die();

/**
 * save_unlinked_image_command
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
class save_unlinked_image_command extends abstract_command_class {

    const SAVE_FILE_INCORRECT_CODE = -1; //indica que un fitxer no s'ha pogut dessar correctament
    const SAVE_FILE_CORRECT_CODE = 1;    //indica que un fitxer s'ha dessat correctament
    const FILENAME_EXISTS_CODE = -2;     //indica que un fitxer ja existeix
    const FILENAME_NOT_EXISTS_CODE = 2;  //indica que un fitxer no existeix
    const UNDEFINED_COMMAND_CODE = -10;  //indica que una comanda no estava definida
    //Comandas
    const COMMAND_PARAM = "do";
    const EXISTS_IMAGE_NAME_PARAM = "existsImage";
    const SAVE_IMAGE_PARAM = "saveImage";
    const IMAGE_NAME_PARAM = "imageName";
    //Parametres del fitxer
    const FILE_PARAM = "file";
    const PNG_MIME_TYPE = "image/png";

    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = (!@file_exists(DOKU_INC."debug"));

        $this->types[self::COMMAND_PARAM] = self::T_STRING;
        $this->types[self::EXISTS_IMAGE_NAME_PARAM] = self::T_STRING;
        $this->types[self::SAVE_IMAGE_PARAM] = self::T_STRING;
        $this->types[self::IMAGE_NAME_PARAM] = self::T_STRING;
        $this->types[self::FILE_PARAM] = self::T_FILE;

        $defaultValues=array(self::COMMAND_PARAM => self::EXISTS_IMAGE_NAME_PARAM);
        $this->setParameters($defaultValues);
    }

    protected function process() {
        $response = self::UNDEFINED_COMMAND_CODE;
        if (array_key_exists(self::COMMAND_PARAM, $this->params)) {
            $do = $this->params[self::COMMAND_PARAM];
            switch ($do) {
                case self::EXISTS_IMAGE_NAME_PARAM:
                    $response = $this->nameExists();
                    break;
                case self::SAVE_IMAGE_PARAM:
                    $response = $this->saveImage();
                    break;
                default:
                    break;
            }
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {
        $responseCode = $response;
        $info = "";
        switch ($responseCode) {
            case self::SAVE_FILE_INCORRECT_CODE:
                $info = $this->getLang('saveFileIncorrect');
                break;
            case self::SAVE_FILE_CORRECT_CODE:
                $info = $this->getLang('saveFileCorrect');
                break;
            case self::FILENAME_EXISTS_CODE:
                $info = $this->getLang('filenameExists');
                break;
            case self::FILENAME_NOT_EXISTS_CODE:
                $info = $this->getLang('filenameNotExists');
                break;
            case self::UNDEFINED_COMMAND_CODE:
                $info = $this->getLang('undefinedCommand');
                break;
            default:
                $info = $this->getLang('unexpectedError');
                break;
        }
        $ret->addCodeTypeResponse($responseCode, $info);
    }

    /**
     * Comprova si existeix el nom de la imatge.
     * @return int response code information
     */
    private function nameExists() {
        $response = self::UNDEFINED_COMMAND_CODE;
        if (array_key_exists(self::IMAGE_NAME_PARAM, $this->params)) {
            $imageName = $this->params[self::IMAGE_NAME_PARAM];
            $imagePath = $this->getImageRepositoryDir() . $imageName;
            if (file_exists($imagePath)) {
                $response = self::FILENAME_EXISTS_CODE;
            } else {
                $response = self::FILENAME_NOT_EXISTS_CODE;
            }
        }
        return $response;
    }

    /**
     * Desa la imatge en el directori correcte.
     * @return int response code information
     */
    private function saveImage() {
        $response = self::SAVE_FILE_INCORRECT_CODE;
        if (array_key_exists(self::FILE_PARAM, $this->params)) {
            $file = $this->params[self::FILE_PARAM];
            if ($file[self::ERROR_PARAM] == UPLOAD_ERR_OK
                    && $file[self::FILE_TYPE_PARAM] == self::PNG_MIME_TYPE
                    && is_uploaded_file($file[self::FILE_CONTENT_PARAM])) {

                $nameImage = $file[self::FILENAME_PARAM];
                $imagePath = $this->getImageRepositoryDir() . $nameImage;
                $nsImage = str_replace("/", ":", $this->getConf('processingImageRepository'));
                $filePath = $file[self::FILE_CONTENT_PARAM];//path del fitxer temporal
                //Decodifica el fitxer
                $contentFile = base64_decode(file_get_contents($filePath));
                file_put_contents($filePath, $contentFile);

                if (file_exists($imagePath)) {//El nom de fitxer ja existeix
                    unlink($filePath); //Elimina el fitxer temporal
                    $response = self::FILENAME_EXISTS_CODE;
                }
                else {
                    $params = array('nsTarget' => $nsImage,
                                    'mediaName' => $nameImage,
                                    'filePathSource' => $filePath,
                                    'overWrite' => FALSE
                              );
                    $action = $this->getModelManager()->getActionInstance("UploadMediaAction");
                    $content = $action->get($params);
                    if ($content["resultCode"] == 0) {
                        $response = self::SAVE_FILE_CORRECT_CODE;
                    }
                }
            }
        }
        return $response;
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getImageRepositoryDir() {
        global $conf;
        return $conf['mediadir'] . "/". $this->getConf('processingImageRepository');
    }

}
