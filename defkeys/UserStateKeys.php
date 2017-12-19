<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
require_once(DOKU_INC . 'lib/plugins/ajaxcommand/defkeys/RequestParameterKeys.php');

class UserStateKeys extends RequestParameterKeys
{
    // Editors
    const KEY_DOJO = "Dojo";
    const KEY_ACE = "ACE";

    // Content Formats
    const KEY_DOKUWIKI = "DW";
    const KEY_HTML = "HTML";

}
