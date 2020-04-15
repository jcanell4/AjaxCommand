<?php
/**
 * Class comment_command
 *
 * TODO: Implementar aquesta classe, per ara només està implementada la banda del client amb l'enviament de les dades
 *
 * @author Xavier Garcia <xaviergaro.dev@gmail.com>
 */
if (!defined('DOKU_INC')) die();

class comment_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
//        $this->types[PageKeys::KEY_ID] = self::T_STRING;
//        $this->types[PageKeys::KEY_AUTO] = self::T_BOOLEAN;
//        $this->types[PageKeys::KEY_DISCARD_DRAFT] = self::T_BOOLEAN;
//        $this->types[PageKeys::KEY_KEEP_DRAFT] = self::T_BOOLEAN;
//        $this->types[PageKeys::KEY_NO_RESPONSE] = self::T_BOOLEAN;
//        $this->types[PageKeys::KEY_UNLOCK] = self::T_BOOLEAN;

//        $this->setParameterDefaultValues(array(PageKeys::KEY_NO_RESPONSE => FALSE));
    }

    /**
     * Processa un comentari
     * @return string[]
     */
    protected function process() {
        //$action = $this->getModelManager()->getActionInstance("CancelEditPageAction", ['format' => $this->getFormat()]);
        //$content = $action->get($this->params);
        //return $content;


        // params de la petició:
        //      ns: ns del document
        //      commentId: referència del comentari
        //      oldContent: contingut anterior del comentari per eliminar o editar
        //      newContent: contingut nou per editar o afegit
        //      action: acció a portar a terme mitjançant aquest command
        //      signature: signatura que apareix als comentaris. Es necessaria per actualitzar la edició i quan
        //          s'afegeix una nova resposta


        // La idea es fer la eliminació o sustitució dels comentaris a partir del contingut de 'oldContent' en lloc de
        // fer servir un altre identificador per les respostes

        // action:
        //      resolve: Eliminar el comentari (resolt)
        //      add: Afegir resposta
        //      edit: Editar resposta
        //      remove: Eliminar resposta


        // resposta:
        //      no cal enviar res, potser un missatge de confirmació que surti al area d'info?


        return [];
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param mixed $contentData // TODO[Xavi] No es fa servir per a res?
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     */
    protected function getDefaultResponse($contentData, &$ret) {
        //TODO[Xavi] $contentData no te cap valor?
        $ret->addHtmlDoc(
                $contentData[PageKeys::KEY_ID],
                $contentData[PageKeys::KEY_NS],
                $contentData["title"],
                $contentData["content"]
            );
    }
}
