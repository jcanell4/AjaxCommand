<?php
/**
 * Class profile_command: comanda corresponent a "El meu perfil"
 * @culpable Rafael
*/
if (!defined('DOKU_INC')) die();

class profile_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AjaxKeys::KEY_DO] = self::T_STRING;
        $this->setParameters( [AjaxKeys::KEY_DO => AjaxKeys::KEY_PROFILE] );
    }

    /**
    * Retorna la pàgina corresponent a la tasca d'administració 'page'.
    * @return array amb la informació de la pàgina formatada amb 'id', 'tittle' i 'content'
    */
    protected function process() {
        $action = $this->getModelManager()->getActionInstance("ProfileAction");
        $content = $action->get($this->params);
        return $content;
    }

    protected function getDefaultResponse($contentData, &$responseGenerator) {}

}
