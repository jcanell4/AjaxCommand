<?php
/**
 * @author josep, Rafa
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . "ajaxcommand/defkeys/AjaxKeys.php");

class RequestParameterKeys extends AjaxKeys {
    const PLUGIN           = 'plugin';
    const FIRST_KEY        = 'first';
    const SHOW_CHANGES_KEY = 'show_changes';
}
