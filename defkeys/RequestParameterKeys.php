<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
require_once (DOKU_INC . 'lib/plugins/ownInit/WikiGlobalConfig.php');

class RequestParameterKeys {
    const DO_KEY        = 'do';
    const CALL_KEY      = 'call';
    const PROJECT_TYPE  = 'projectType';
    const KEY_ID        = "id";
    const KEY_DO        = "do";
}
