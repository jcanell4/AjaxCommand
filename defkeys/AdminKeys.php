<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
require_once (DOKU_INC . 'lib/plugins/ajaxcommand/defkeys/RequestParameterKeys.php');

class AdminKeys extends RequestParameterKeys {
    const KEY_ID    = "id";
    const KEY_DO    = "do";
    const KEY_TASK  = "task";
    const KEY_PAGE  = "page";
}
