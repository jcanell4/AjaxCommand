<?php
/**
 * media_command: Este comando es llamado cuando:
 * - se carga una página que contiene imágenes
 * - se llama al elemento 'Media manager' del menú
 */
if (!defined('DOKU_INC')) die();

class media_command extends abstract_writer_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[MediaKeys::KEY_ID] = self::T_STRING;
        $this->types[MediaKeys::KEY_MEDIA] = self::T_STRING;
        $this->types[MediaKeys::KEY_IMAGE] = self::T_STRING;
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
            $this->params[MediaKeys::KEY_IMAGE] = $this->params[MediaKeys::KEY_MEDIA];
        }
        if ($this->params[MediaKeys::KEY_ID]) {
            $this->params[MediaKeys::KEY_FROM_ID] = $this->params[MediaKeys::KEY_ID];
        }
        if ($this->params[MediaKeys::KEY_DELETE]) {
            $params = array(MediaKeys::KEY_NS => $this->params[MediaKeys::KEY_NS],
                            MediaKeys::KEY_ID => $this->params[MediaKeys::KEY_ID],
                            MediaKeys::KEY_DO => $this->params[MediaKeys::KEY_DO],
                            MediaKeys::KEY_DELETE => $this->params[MediaKeys::KEY_DELETE]
                      );
            $action = $this->getModelManager()->getActionInstance("DeleteMediaAction");
            $contentData = $action->get($params);

        }elseif ($this->params[MediaKeys::KEY_IS_UPLOAD]) {
            $params = array(MediaKeys::KEY_NS       => $this->params[MediaKeys::KEY_NS],
                            MediaKeys::KEY_ID       => $this->params[MediaKeys::KEY_ID],
                            MediaKeys::KEY_DO       => $this->params[MediaKeys::KEY_DO],
                            MediaKeys::KEY_MEDIA_ID => $this->params[MediaKeys::KEY_MEDIA_ID],
                            MediaKeys::KEY_UPLOAD   => $this->params[MediaKeys::KEY_UPLOAD]
                      );
            $action = $this->getModelManager()->getActionInstance("UploadMediaAction");
            $contentData = $action->get($params);

        }else {
            $action = $this->getModelManager()->getActionInstance("ViewMediaAction", $this->params);
            $contentData = $action->get($this->params);
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
                $contentData[MediaKeys::KEY_IMAGE],
                null,
                $contentData[MediaKeys::KEY_IMAGE_TITLE],
                $contentData[PageKeys::KEY_CONTENT]
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
