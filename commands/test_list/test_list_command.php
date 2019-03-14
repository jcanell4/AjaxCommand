<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");

require_once(DOKU_COMMAND . 'defkeys/PageKeys.php');
require_once(DOKU_PLUGIN . 'wikiiocmodel/actions/UserListAction.php');

/**
 * Class test_list_command
 * @author Xavier Garcia <xaviergaro.dev@gmail.com>
 */
class test_list_command extends abstract_command_class
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;

        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_DO] = self::T_STRING;
        $this->types[PageKeys::KEY_FILTER] = self::T_STRING;
    }

    protected function getDefaultResponse($response, &$responseGenerator)
    {
        $responseGenerator->addArrayTypeResponse($response);
    }

    protected function process()
    {

        $response = [
            [
                'field1' => 'value1a',
                'field2' => 'value2a',
                'fieldn' => 'valuena',
            ],
            [
                'field1' => 'value1b',
                'field2' => 'value2b',
                'fieldn' => 'valuenb',
            ],
            [
                'field1' => 'value1c',
                'field2' => 'value2c',
                'fieldn' => 'valuenc',
            ],
            [
                'field1' => 'value1d',
                'field2' => 'value2d',
                'fieldn' => 'valuend',
            ],
        ];

        // El filtre ha de ser un array per filtrar per múltiples valors. El filtre s'aplica com a 'like' no cerca idéntica:
        //  [
        //      'field'=>'value'
        //  ]

        if (isset($this->params[PageKeys::KEY_FILTER])) {
           // TODO: filtrar el $response per retornar només els valors que coincideixin amb els filtres
        }


        return $response;
    }
}
