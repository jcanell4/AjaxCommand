<?php
if(!defined('DOKU_INC')) die();

/**
 * Class notify_command
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class notify_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AjaxKeys::KEY_DO] = self::T_STRING;
        $this->types['message'] = self::T_STRING;
        $this->types['to'] = self::T_STRING;
        $this->types['params'] = self::T_STRING;
        $this->types['changes'] = self::T_STRING;
        $this->types['since'] = self::T_INTEGER;

        $this->setParameterDefaultValues(array("since" => 0));
    }


    protected function process() {
        if (isset($this->params['params'])) {
            $this->params['params'] = json_decode($this->params['params'], true);
        }
        if (isset($this->params['changes'])) {
            $this->params['changes'] = json_decode($this->params['changes'], true);
        }
//        return $this->modelWrapper->notify($this->params);
        $action = $this->modelManager->getActionInstance("NotifyAction", $this->getModelWrapper()->getPersistenceEngine());
        $contentData = $action->get($this->params, false);
        return $contentData;
    }

    protected function getDefaultResponse($response, &$ajaxCmdResponseGenerator) {

        if (isset($response['info'])) {
            $ajaxCmdResponseGenerator->addInfoDta($response['info']);
        }

        foreach ($response['notifications'] as $notification) {
            $ajaxCmdResponseGenerator->addNotificationResponse($notification['action'], $notification['params']);
        }

    }

    public function getAuthorizationType() {
        return "_none";
    }
}
