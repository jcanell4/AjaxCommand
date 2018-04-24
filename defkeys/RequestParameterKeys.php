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

    const KEY_DATE           = "date";
    const KEY_REV            = "rev";
    const KEY_CANCEL         = "cancel";
    const KEY_CLOSE          = "close";
    const KEY_KEEP_DRAFT     = "keep_draft";
    const KEY_NO_RESPONSE    = "no_response";
    const KEY_DISCARD_CHANGES= "discard_changes";
    const KEY_CODETYPE       = "codeType";
    const KEY_LEAVERESOURCE  = "leaveResource";
    const KEY_TO_REQUIRE     = "to_require";
    const KEY_REFRESH        = "refresh";

    const KEY_RECOVER_DRAFT       = "recover_draft";
    const KEY_RECOVER_LOCAL_DRAFT = "recover_local_draft";

}
