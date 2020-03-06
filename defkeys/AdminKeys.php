<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'ajaxcommand/defkeys/RequestParameterKeys.php');
require_once (DOKU_PLUGIN . 'ajaxcommand/defkeys/UserStateKeys.php');

class AdminKeys extends RequestParameterKeys {
    const KEY_TASK  = "task";
    const KEY_PAGE  = "page";
    const KEY_DOJO = UserStateKeys::KEY_DOJO;
    const KEY_ACE = UserStateKeys::KEY_ACE;
}
