<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN'))  define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Clase del comando que controla la presentación del detalle de un 'media' (un archivo que no es texto plano)
 */
class mediadetails_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['image'] = abstract_command_class::T_STRING;
        $this->types['fromId'] = abstract_command_class::T_STRING;
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['media'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;
        $this->types['isupload'] = abstract_command_class::T_STRING;
        $this->needMediaInfo = TRUE;
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {

        if ($this->params['media']) {
            $this->params['image'] = $this->params['media'];
        }
        if($this->params['delete']){
            $contentData = $this->modelWrapper->deleteMediaManager($this->params);
//        }else if($this->params[MediaKeys::KEY_IS_UPLOAD]){
//            $contentData = $this->modelWrapper->uploadMediaManager($this->params);
        }else{
            $contentData = $this->modelWrapper->getMediaDetails($this->params['image']);
        }
        
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

        $responseGenerator->addHtmlDoc(
                $contentData["image"], null, 
                $contentData["imageTitle"],
                $contentData["content"]
        );
    }

    /**
     * @return string (nom del command, a partir del nom de la clase,
     *                 modificat pels valors de $params per a definir subclasses específiques
     *                 amb autoritzacions específiques)
     */
    public function getAuthorizationType() {
        $className = preg_replace('/_command$/', '', get_class($this));
        if ($this->params['delete']) {
            $className .= "_delete";
        }
        elseif ($this->params['isupload'] === 'upload') {
            $className .= ($this->params['ow'] === "1") ? "_delete" : "_upload";
        }
        elseif ($this->params['tab_details'] === 'edit' || $this->params['mediado'] === 'save') {
            $className .= "_edit";
        }
        return $className;
    }

}
