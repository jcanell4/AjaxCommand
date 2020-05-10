<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();

class MediaKeys extends RequestParameterKeys {
    const KEY_FROM_ID           = "fromId";
    const KEY_IMAGE             = "image";
    const KEY_IMG               = "img";
    const KEY_IMAGE_TITLE       = "imageTitle";
    const KEY_FILE_PATH_SOURCE  = "filePathSource";
    const KEY_OVERWRITE         = "ow";
    const KEY_NS_TARGET         = "nsTarget";
    const KEY_MEDIA_NAME        = "mediaName";
    const KEY_REV               = "rev";
    const KEY_META              = "meta";
    const KEY_EDIT              = "edit";
    const KEY_DELETE            = "delete";
    const KEY_SAVE              = "save";
    const KEY_UPLOAD            = 'upload';
    const KEY_MEDIA             = "media";
    const KEY_MEDIA_DO          = "mediado";
    const KEY_MEDIA_ID          = 'mediaid';
    const KEY_NAME              = 'name';
    const KEY_ERROR             = 'error';
    const KEY_TMP_NAME          = 'tmp_name';
    const KEY_IS_UPLOAD         = 'isupload';
    const KEY_QUERY             = 'q';
    const KEY_SORT              = 'sort';
}
