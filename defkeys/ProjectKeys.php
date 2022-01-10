<?php
/**
 * @author Xavier García <xaviergaro.dev@gmail.com>
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();

class ProjectKeys extends RequestParameterKeys  {

    const KEY_PROJECT_TYPE = AjaxKeys::PROJECT_TYPE;

    const KEY_PROJECT  = "project";
    const KEY_EDIT     = "edit";
    const KEY_VIEW     = "view";
    const KEY_PARTIAL  = "partial";
    const KEY_GENERATE = "generate";
    const KEY_SAVE     = "save";
    const KEY_REVERT   = "revert";
    const KEY_CREATE   = "create";
    const KEY_FTP_SEND = "ftpsend";
    const KEY_IMPORT   = "import";
    const KEY_WORKFLOW = "workflow";

    const KEY_PROJECT_ID        = "projectId";
    const KEY_DATA_ERROR_LIST   = "dataErrorList";
    const KEY_PROJECT_METADATA  = "projectMetaData";
    const KEY_PROJECT_EXTRADATA = "projectExtraData";
    const KEY_PROJECT_VIEWDATA  = "projectViewData";
    const KEY_GENERATED         = "generated";
    const KEY_CREATE_PROJECT    = "create_project";
    const KEY_CREATE_SUBPROJECT = "create_subproject";
    const KEY_NEW_DOCUMENT      = "new_document";
    const KEY_NEW_FOLDER        = "new_folder";

    const KEY_MODE        = "mode";
    const KEY_RENDER_TYPE = "renderType";
    const KEY_FILE_TYPE   = "filetype";
    const KEY_ISONVIEW    = "isOnView";

    const KEY_NAME      = "name";
    const KEY_NSPROJECT = "nsproject";

    const KEY_NEWNAME   = "newname";
    const KEY_OLD_ID    = "old_id";
    const KEY_OLD_NS    = "old_ns";
    const KEY_FTPID     = "ftpId";

    const KEY_FTP_PROJECT          = "ftp_project";
    const KEY_DUPLICATE            = "duplicate";
    const KEY_DUPLICATE_PROJECT    = "duplicate_project";
    const KEY_RENAME               = "rename";
    const KEY_RENAME_PROJECT       = "rename_project";
    const KEY_REMOVE_PROJECT       = "remove_project";
    const KEY_REVERT_PROJECT       = "revert_project";
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

    const KEY_METADATA_PROJECT_STRUCTURE  = "metaDataProjectStructure";
    const KEY_METADATA_CLASSES_NAMESPACES = "metaDataClassesNameSpaces";
    const KEY_METADATA_COMPONENT_TYPES    = "metaDataComponentTypes";
    const KEY_METADATA_PROJECT_CONFIG     = "metaDataProjectConfig";
    const KEY_METADATA_FTP_SENDER         = "metaDataFtpSender";
    const KEY_METADATA_EXPORT             = "metaDataExport";
    const KEY_MD_CT_SUBPROJECTS           = "subprojects";
    const KEY_MD_CT_DOCUMENTS             = "documents";
    const KEY_MD_CT_FOLDERS               = "folders";
    const KEY_MD_PROJECTTYPECONFIGFILE    = "projectTypeConfigFile";

    const VIEW_CONFIG_NAME                = "viewConfigName";
}
