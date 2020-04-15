<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();

class AdminKeys extends RequestParameterKeys {
    const KEY_TASK  = "task";
    const KEY_PAGE  = "page";
    const KEY_DOJO = UserStateKeys::KEY_DOJO;
    const KEY_ACE = UserStateKeys::KEY_ACE;
}
