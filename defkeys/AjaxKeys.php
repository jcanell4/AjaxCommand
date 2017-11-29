<?php
/**
 * Definición de constantes para comandos Ajax
 * @culpable by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
require_once (DOKU_INC . 'lib/plugins/ownInit/WikiGlobalConfig.php');
if (!defined('DOKU_TPL_INCDIR')) define('DOKU_TPL_INCDIR', WikiGlobalConfig::tplIncDir());

class AjaxKeys {
    const KEY_ID        = "id";
    const KEY_NS        = "ns";
    const KEY_DO        = "do";
    const CALL_KEY      = 'call';
    const PROJECT_TYPE  = 'projectType';
}