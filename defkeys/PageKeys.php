<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
require_once (DOKU_INC . 'lib/plugins/ajaxcommand/defkeys/RequestParameterKeys.php');

class PageKeys extends RequestParameterKeys {

    //const KEY_ID        = "id";
    //const KEY_DO        = "do";
    const KEY_REV       = "rev";
    const KEY_RANGE     = "range";
    const KEY_DATE      = "date";
    const KEY_PRE       = "prefix";
    const KEY_TEXT      = "text";
    const KEY_WIKITEXT  = "wikitext";
    const KEY_SUF       = "suffix";
    const KEY_SUM       = "summary";
    const KEY_MINOR     = "minor";
    const KEY_TEMPLATE  = "template";

    const KEY_IN_EDITING_CHUNKS = "editing_chunks";
    const KEY_EDITING_CHUNKS    = "editingChunks";
    const KEY_SECTION_ID        = "section_id";
    const KEY_SELECTED          = "selected";

    const KEY_TO_REQUIRE            = "to_require";
    const KEY_FORCE_REQUIRE         = "force_require";
    const KEY_RECOVER_DRAFT         = "recover_draft";
    const KEY_RECOVER_LOCAL_DRAFT   = "recover_local";
    const KEY_DISCARD_DRAFT         = "discard_draft";
    const KEY_KEEP_DRAFT            = "keep_draft";

    const KEY_PARAMS    = "params"; // response
    const KEY_USER_ID   = "user_id";

    const DISCARD_CHANGES = "discard_changes";

    const NO_DRAFT            = "none";
    const PARTIAL_DRAFT       = "partial";
    const FULL_DRAFT          = "full";
    const LOCAL_PARTIAL_DRAFT = "local_partial";
    const LOCAL_FULL_DRAFT    = "local_full";

    const STRUCTURED_LAST_LOCAL_DRAFT_TIME  = "structured_last_local_draft_time";
    const FULL_LAST_LOCAL_DRAFT_TIME        = "full_last_local_draft_time";

    const KEY_LOCK_STATE    = "lock_state";
    const KEY_INFO          = "info";

    const KEY_NO_RESPONSE   = "no_response";
    const KEY_AUTO          = "auto";
    const KEY_REFRESH       = "refresh";
}
