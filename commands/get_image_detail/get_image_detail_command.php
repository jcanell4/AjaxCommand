<?php
if(!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC."lib/plugins/ajaxcommand/");
require_once DOKU_COMMAND . "defkeys/MediaKeys.php";

/**
 * Class get_image_detail_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class get_image_detail_command extends abstract_command_class {
    const KEY_IMAGE_ID = "imageId";

    public function __construct() {
        parent::__construct();
        $this->types[self::KEY_IMAGE_ID] = self::T_STRING;
        $this->types[MediaKeys::KEY_ID] = self::T_STRING;
        $this->types[MediaKeys::KEY_FROM_ID] = self::T_STRING;
        $this->types[MediaKeys::KEY_MEDIA] = self::T_STRING;
    }

    /**
     * Retorna el detall de la imatge
     * @return array amb el detall de la imatge, el títol del quadre de diàleg i la ruta de la imatge
     */
    protected function process() {
        if ($this->params[MediaKeys::KEY_MEDIA]) {
            $this->params[self::KEY_IMAGE_ID] = $this->params[MediaKeys::KEY_MEDIA];
        }
        if ($this->params[MediaKeys::KEY_ID]) {
            $this->params[MediaKeys::KEY_FROM_ID] = $this->params[MediaKeys::KEY_ID];
        }
        $contentData = $this->modelAdapter->getImageDetail(
                                              $this->params[self::KEY_IMAGE_ID],
                                              $this->params[MediaKeys::KEY_FROM_ID]
                                            );
        return $contentData;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param array                    $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addProcessFunction(
                true,
                "ioc/dokuwiki/processShowingImage",
                $contentData
        );
    }
}