<?php
/**
 * ResponseHandlerKeys
 * @culpable Rafa
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'ajaxcommand/defkeys/RequestParameterKeys.php');

class ResponseHandlerKeys extends RequestParameterKeys {
    const LOGIN  = "login";
    const PAGE   = "page";
    const EDIT   = "edit";
    const CANCEL = "cancel";
    const CLOSE  = "close";
    const SAVE   = "save";
    const MEDIA  = "media";
    const MEDIADETAILS   = "mediadetails";
    const ADMIN_TASK     = "admin_task";
    const ADMIN_TAB      = "admin_tab";
    const PRINT_ACTION   = "print";
    const PREVIEW_ACTION = "preview";
    const PROJECT        = "project";
    const PROFILE        = "profile";
    const FIRST_POSITION = "first";
    const TEXT           = "text";

}
