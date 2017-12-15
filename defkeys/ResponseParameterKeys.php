<?php
/**
 * Description of ResponseParameterKeys
 * @author Xavi
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'ajaxcommand/defkeys/AjaxKeys.php');

class ResponseParameterKeys extends AjaxKeys {
    const FIRST_POSITION = 'first';
    const TEXT = 'text';
}
