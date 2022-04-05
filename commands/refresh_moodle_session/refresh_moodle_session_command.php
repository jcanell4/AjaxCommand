<?php
if(!defined('DOKU_INC')) die();

/**
 * Class refresh_moodle_session_command
 */
class refresh_moodle_session_command extends abstract_command_class {

    protected function process() {
        if (isset($this->params['moodleToken']) && $this->params['moodleToken'] && $this->params['moodleToken']!="null") {
            $action = $this->getModelManager()->getActionInstance("RefreshMoodleSessionAction", FALSE);
            $contentData = $action->get($this->params);
        }
        return $contentData;
    }

    protected function getDefaultResponse($response, &$ajaxCmdResponseGenerator) {
        if (isset($response['info'])) {
            $ajaxCmdResponseGenerator->addInfoDta($response['info']);
        }
    }

    public function getAuthorizationType() {
        return "_none";
    }
}
