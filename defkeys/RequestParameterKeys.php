<?php
/**
 * @author josep, Rafa
 */
if (!defined('DOKU_INC')) die();

class RequestParameterKeys extends AjaxKeys {
    const PLUGIN           = 'plugin';
    const FIRST_KEY        = 'first';
    const SHOW_CHANGES_KEY = 'show_changes';

    const KEY_DATE           = "date";
    const KEY_REV            = "rev";
    const KEY_CANCEL         = "cancel";
    const KEY_CLOSE          = "close";
    const KEY_KEEP_DRAFT     = "keep_draft";
    const KEY_HAS_DRAFT      = "hasDraft";
    const KEY_NO_RESPONSE    = "no_response";
    const KEY_DISCARD_CHANGES= "discard_changes";
    const KEY_CODETYPE       = "codeType";
    const KEY_LEAVERESOURCE  = "leaveResource";
    const KEY_TO_REQUIRE     = "to_require";
    const KEY_REFRESH        = "refresh";
    const KEY_INFO           = "info";

    const KEY_RECOVER_DRAFT       = "recover_draft";
    const KEY_RECOVER_LOCAL_DRAFT = "recover_local_draft";

    const VAL_CODETYPE_OK     = 0;   //retorno correcto
    const VAL_CODETYPE_ERROR  = -1;  //retorno con error
    const VAL_CODETYPE_REMOVE = 1;   //retorno para el comando remove_project
}
