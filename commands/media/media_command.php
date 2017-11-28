<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC."lib/plugins/ajaxcommand/");
require_once DOKU_COMMAND . "defkeys/MediaKeys.php";

class media_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[MediaKeys::KEY_ID] = self::T_STRING;
        $this->types[MediaKeys::KEY_MEDIA] = self::T_STRING;
        $this->types[MediaKeys::KEY_IMAGE_ID] = self::T_STRING;
        $this->types[MediaKeys::KEY_FROM_ID] = self::T_STRING;
        $this->types[MediaKeys::KEY_REV] = self::T_STRING;
        $this->types[MediaKeys::KEY_IS_UPLOAD] = self::T_STRING;
        $this->types[MediaKeys::KEY_OVERWRITE] = self::T_BOOLEAN;
        $this->types[MediaKeys::KEY_UPLOAD] = self::T_FILE;
        $this->needMediaInfo = TRUE;
    }

    public function setParameters($params) {
        if ($params[MediaKeys::KEY_ID]){
            if ($params[MediaKeys::KEY_NS] && $params[MediaKeys::KEY_ID] === $params[MediaKeys::KEY_NS] ){
                $params[MediaKeys::KEY_ID] = $params[MediaKeys::KEY_NS].':';
            }
        }else if($params[MediaKeys::KEY_NS]){
            $params[MediaKeys::KEY_ID] = $params[MediaKeys::KEY_NS].':';
        }
        parent::setParameters($params);
    }
    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        if ($this->params[MediaKeys::KEY_MEDIA]) {
            $this->params[MediaKeys::KEY_IMAGE_ID] = $this->params[MediaKeys::KEY_MEDIA];
        }
        if ($this->params[MediaKeys::KEY_ID]) {
            $this->params['fromId'] = $this->params[MediaKeys::KEY_ID];
        }
        if($this->params[MediaKeys::KEY_DELETE]){
            $contentData =$this->modelWrapper->deleteMediaManager($this->params);
        }else if($this->params[MediaKeys::KEY_IS_UPLOAD]){
            $contentData =$this->modelWrapper->uploadMediaManager($this->params);
        }else{
            $contentData = $this->modelWrapper->getMediaManager(
            $this->params[MediaKeys::KEY_IMAGE_ID], $this->params[MediaKeys::KEY_FROM_ID], $this->params[MediaKeys::KEY_REV]
            );
        }
        return $contentData;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param array                    $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addHtmlDoc(
                $contentData[MediaKeys::KEY_IMAGE_ID],
                null,
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
        if ($this->params[MediaKeys::KEY_DELETE]) {
            $className .= "_delete";
        }
        elseif ($this->params[MediaKeys::KEY_IS_UPLOAD] === 'upload') {
            $className .= "_upload";
        }
        elseif ($this->params['tab_details'] === 'edit' || $this->params[MediaKeys::KEY_MEDIA_DO] === 'save') {
            $className .= "_edit";
        }
        return $className;
    }

}
