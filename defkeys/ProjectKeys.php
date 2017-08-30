<?php
/**
 * @author Xavier García <xaviergaro.dev@gmail.com>
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
require_once(DOKU_INC.'lib/plugins/ajaxcommand/defkeys/RequestParameterKeys.php');

class ProjectKeys extends RequestParameterKeys  {
    const KEY_NS           = "ns";
    const KEY_PROJECT_TYPE = RequestParameterKeys::PROJECT_TYPE;

    const KEY_EDIT      = "edit";
    const KEY_CREATE    = "create";
    const KEY_GENERATE  = "generate";
    const KEY_SAVE      = "save";

    const KEY_ROL             = "rol";
    const KEY_FILTER          = "filter";
    const KEY_ID_RESOURCE     = "idResource";
    const KEY_PERSISTENCE     = "persistence";
    const KEY_DEFAULTSUBSET   = "defaultSubSet";
    const VAL_DEFAULTSUBSET   = "main";
    const KEY_METADATA_SUBSET = "metaDataSubSet";
    const KEY_METADATA_VALUE  = "metaDataValue";
    const KEY_PROJECT_FILENAME= "projectFileName";
    const KEY_PROJECT_FILEPATH= "projectFilePath";

    const KEY_METADATA_PROJECT_STRUCTURE  = "metaDataProjectStructure";
    const KEY_METADATA_CLASSES_NAMESPACES = "metaDataClassesNameSpaces";
}
