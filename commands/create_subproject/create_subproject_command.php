<?php
/*
 * create_subproject_command
 * @culpable: Rafael
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . "lib/plugins/");
include_once DOKU_PLUGIN . "ajaxcommand/commands/project/project_command.php";

class create_subproject_command extends project_command {

    protected function process() {
        $response = array();
        $response[ProjectKeys::KEY_ID] = $this->params['new_id'];
        $response['urlBase'] = "lib/exe/ioc_ajax.php?call=project";
        $response['query'] = "do=".ProjectKeys::KEY_CREATE_PROJECT;
        $response['data'] = [ProjectKeys::KEY_ID => str_replace(":", "_", $this->params['new_id']),
                             ProjectKeys::KEY_PROJECT_TYPE => $this->params['new_projectType'],
                             ProjectKeys::KEY_METADATA_SUBSET => $this->params[ProjectKeys::KEY_METADATA_SUBSET]
                            ];
        return $response;
    }

    function getDefaultResponse( $response, &$ret ) {
        $data = $response['data'];
        $data[ProjectKeys::KEY_ID] = $this->params['new_id'];
        $ret->addProcessFunction(true, "ioc/dokuwiki/recallCommand",
                                 ['urlBase' => $response['urlBase'],
                                  'method' => "post",
                                  'query' => $response['query'],
                                  'data' => $data
                                 ]
                                );
    }

    function postResponse($responseData, &$ajaxCmdResponseGenerator) {
        $responseData['projectExtraData'][ProjectKeys::PROJECT_TYPE] = $responseData['data'][ProjectKeys::PROJECT_TYPE];
        parent::postResponse($responseData, $ajaxCmdResponseGenerator);
    }
}
