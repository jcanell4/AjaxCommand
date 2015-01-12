<?php

if (!defined('DOKU_INC'))
    die();
if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND'))
    define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class page_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class media_command extends abstract_command_class {

    /**
     * El constructor estableix els tipus de 'id' i 'rev' i el valor per defecte de 'id' com a 'start'. i l'estableix
     * com a paràmetre.
     */
    public function __construct() {
        parent::__construct();
        $this->types['imageId'] = abstract_command_class::T_STRING;
        $this->types['fromId'] = abstract_command_class::T_STRING;
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['media'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;
        //getMediaManager($imageId = NULL, $fromPage = NULL, $prev = NULL)
        //getImageDetail($imageId, $fromPage = NULL)
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     *
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        if ($this->params['media']) {
            $this->params['imageId'] = $this->params['media'];
        }
        if ($this->params['id']) {
            $this->params['fromId'] = $this->params['id'];
        }
        $contentData = $this->modelWrapper->getMediaManager(
                $this->params['imageId'], $this->params['fromId'], $this->params['rev']
        );
        return $contentData;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param array                    $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {
        //addHtmlDoc($id, $ns, $title, $content)
        /*$responseGenerator->addHtmlDoc(
                $contentData["imageId"], $contentData["imageTitle"], 
                $contentData["fromId"], $contentData["modifyImageLabel"], 
                $contentData["closeDialogLabel"], $contentData["content"]
        );*/
        $responseGenerator->addHtmlDoc(
                $contentData["imageId"], null, 
                $contentData["imageTitle"],
                $contentData["content"]
        );
        
    }

}
