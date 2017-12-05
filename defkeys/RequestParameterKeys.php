<?php
/**
 * @author josep, Rafa
 */
if (!defined('DOKU_INC')) die();
require_once (DOKU_INC . "lib/plugins/ajaxcommand/defkeys/AjaxKeys.php");

class RequestParameterKeys extends AjaxKeys {
    const PLUGIN           = 'plugin';
    const FIRST_KEY        = 'first';
    const SHOW_CHANGES_KEY = 'show_changes';
}
