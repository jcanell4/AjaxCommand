<?php
/**
 * Definición de constantes para comandos Ajax
 * @culpable by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_TPL_INCDIR')) define('DOKU_TPL_INCDIR', WikiGlobalConfig::tplIncDir());

class AjaxKeys {
    const KEY_ID      = "id";
    const KEY_NS      = "ns";
    const KEY_DO      = "do";
    const KEY_SECTOK  = 'sectok';
    const KEY_CALL    = 'call';
    const KEY_PROFILE = "profile";
    const FORMAT      = "format";

    const PROJECT_TYPE           = "projectType";
    const PROJECT_SOURCE_TYPE    = "projectSourceType";
    const PROJECT_OWNER          = "projectOwner";
    const METADATA_SUBSET        = "metaDataSubSet";
    const VAL_DEFAULTSUBSET      = "main";
    const VAL_DEFAULTPROJECTTYPE = "defaultProject";

    const KEY_ACTIVA_UPDATE_BTN      = "activaUpdateButton";
    const KEY_ACTIVA_FTPSEND_BTN     = "activaFtpSendButton";
    const KEY_FTPSEND_BUTTON         = "ftpSendButton";
    const KEY_ACTIVA_FTP_PROJECT_BTN = "activaFtpProjectButton";
    const KEY_FTP_PROJECT_BUTTON     = "ftpProjectButton";
    const KEY_FTPSEND_HTML           = "ftpsend_html";
    const KEY_FTP_CONFIG             = "ftp_config";
}
