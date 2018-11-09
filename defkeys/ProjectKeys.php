<?php
/**
 * @author Xavier García <xaviergaro.dev@gmail.com>
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
require_once(DOKU_INC.'lib/plugins/ajaxcommand/defkeys/RequestParameterKeys.php');

class ProjectKeys extends RequestParameterKeys  {

    const KEY_PROJECT_TYPE = AjaxKeys::PROJECT_TYPE;
    const REVISION_SUFFIX  = "-rev-";

    const KEY_PROJECT  = "project";
    const KEY_EDIT     = "edit";
    const KEY_VIEW     = "view";
    const KEY_GENERATE = "generate";
    const KEY_SAVE     = "save";
    const KEY_DIFF     = "diff";
    const KEY_REVERT   = "revert";
    const KEY_CREATE   = "create";
    const KEY_CREATE_PROJECT    = "create_project";
    const KEY_CREATE_SUBPROJECT = "create_subproject";
    const KEY_NEW_DOCUMENT      = "new_document";
    const KEY_NEW_FOLDER        = "new_folder";

    const KEY_MODE        = "mode";
    const KEY_RENDER_TYPE = "renderType";
    const KEY_FILE_TYPE   = "filetype";

    const KEY_NAME      = "name";
    const KEY_NSPROJECT = "nsproject";
    const KEY_TYPE      = "type";

    const KEY_SAVE_PROJECT_DRAFT   = "save_project_draft";
    const KEY_REMOVE_PROJECT_DRAFT = "remove_project_draft";

    const KEY_ROL             = "rol";
    const KEY_FILTER          = "filter";
    const KEY_TEMPLATE        = "template";
    const KEY_ID_RESOURCE     = "idResource";
    const KEY_PERSISTENCE     = "persistence";
    const KEY_DEFAULTVIEW     = "defaultView";
    const KEY_DEFAULTSUBSET   = "defaultSubSet";
    const VAL_DEFAULTSUBSET   = "main";
    const KEY_METADATA_VALUE  = "metaDataValue";
    const KEY_METADATA_SUBSET = AjaxKeys::METADATA_SUBSET;
    const KEY_PROJECTTYPE_DIR = AjaxKeys::PROJECT_TYPE_DIR;
    const KEY_PROJECT_FILENAME= "projectFileName";
    const KEY_PROJECT_FILEPATH= "projectFilePath";

    const KEY_METADATA_PROJECT_STRUCTURE  = "metaDataProjectStructure";
    const KEY_METADATA_CLASSES_NAMESPACES = "metaDataClassesNameSpaces";
    const KEY_METADATA_COMPONENT_TYPES    = "metaDataComponentTypes";
    const KEY_METADATA_PROJECT_CONFIG     = "metaDataProjectConfig";
    const KEY_MD_CT_SUBPROJECTS           = "subprojects";
    const KEY_MD_CT_DOCUMENTS             = "documents";
    const KEY_MD_CT_FOLDERS               = "folders";
    const KEY_MD_PROJECTTYPECONFIGFILE    = "projectTypeConfigFile";
    
    const VIEW_CONFIG_NAME                = "viewConfigName";
}
