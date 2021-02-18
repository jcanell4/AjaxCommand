<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();

class PageKeys extends RequestParameterKeys {
    const KEY_RANGE     = "range";
    const KEY_PRE       = "prefix";
    const KEY_TEXT      = "wikitext";  //antes: KEY_TEXT = "text";
    const KEY_WIKITEXT  = "wikitext";
    const KEY_SUF       = "suffix";
    const KEY_SUM       = "summary";
    const KEY_MINOR     = "minor";
    const KEY_TEMPLATE  = "template";
    const KEY_TARGET    = "target";
    const KEY_TITLE     = "title";
    const KEY_CONTENT   = "content";

    const KEY_IN_EDITING_CHUNKS = "editing_chunks";
    const KEY_EDITING_CHUNKS    = "editingChunks";
    const KEY_SECTION_ID        = "section_id";
    const KEY_SELECTED          = "selected";

    const KEY_COPY_REMOTE_DRAFT_TO_LOCAL = "copy_remote_draft";
    const KEY_FORCE_REQUIRE              = "force_require";
    const KEY_DISCARD_DRAFT              = "discard_draft";

    const KEY_UNLOCK     = "unlock";
    const KEY_RELOAD     = "reload";
    const KEY_CANCEL_ALL = "cancel_all";
    const KEY_CLOSE      = "close";
    const KEY_OLD_FOLDER_NAME = "old_folder_name";  //rename folder
    const KEY_NEW_FOLDER_NAME = "new_folder_name";  //rename folder

    const KEY_PARAMS  = "params"; // response
    const KEY_USER_ID = "user_id";
    const KEY_HTML_SC = "html_sc"; //contingut HTML del shortcut

    const NO_DRAFT            = "none";
    const PARTIAL_DRAFT       = "partial";
    const FULL_DRAFT          = "full";
    const LOCAL_PARTIAL_DRAFT = "local_partial";
    const LOCAL_FULL_DRAFT    = "local_full";
    const DISCARD_CHANGES     = RequestParameterKeys::KEY_DISCARD_CHANGES;
    const CHANGE_CHECK        = "changecheck";

    const STRUCTURED_LAST_LOCAL_DRAFT_TIME = "structured_last_local_draft_time";
    const FULL_LAST_LOCAL_DRAFT_TIME       = "full_last_local_draft_time";

    const KEY_LOCK_STATE  = "lock_state";
    const KEY_INFO        = "info";
    const KEY_AUTO        = "auto";

    const KEY_FILTER    = "filter";
    const KEY_START_POS = "start";
    const KEY_PROJECT   = "project";
    const KEY_OFFSET    = "offset";

    const DW_ACT_CREATE        = "create";
    const DW_ACT_DENIED        = "denied";
    const DW_ACT_DRAFTDEL      = "draftdel";
    const DW_ACT_EDIT          = "edit";
    const DW_ACT_EXPORT_ADMIN  = "admin";
    const DW_ACT_LOCK          = "lock";
    const DW_ACT_MEDIA_DETAIL  = "media_detail";
    const DW_ACT_MEDIA_DETAILS = "mediadetails";
    const DW_ACT_MEDIA_MANAGER = "media";
    const DW_ACT_PREVIEW       = "preview";
    const DW_ACT_RECENT        = "recent";
    const DW_ACT_RECOVER       = "recover";
    const DW_ACT_REMOVE        = "remove";
    const DW_ACT_SAVE          = "save";
    const DW_ACT_SAVE_REV      = "save_rev";
    const DW_ACT_SHOW          = "show";
    const DW_DEFAULT_PAGE      = "start";

    const KEY_WIOCCL_STRUCTURE   = "wioccl_structure";
}
