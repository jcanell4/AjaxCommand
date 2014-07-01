<?php

/**
 * Description 
 *
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
if (!defined('DOKU_INC'))
    die();
if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND'))
    define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
if (!defined('DOKU_COMMAND_PDE'))
    define('DOKU_COMMAND_PDE', DOKU_COMMAND . "commands/save_pde_algorithm/");

require_once (DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once (DOKU_COMMAND . 'abstract_command_class.php');
require_once (DOKU_COMMAND_PDE . 'PdeManager.php');


class save_pde_algorithm_command extends abstract_command_class {
    /* Codi d'informació per quan ha anat tot correctament.
     * @return integer Retorna un 0
     */

    static $OK_CODE = 0;


    /* Codi d'informació per quan un algorisme ja existeix.
     * @return integer Retorna un -2
     */
    static $ALGORITHM_EXISTS_CODE = -2;

    /* Codi d'informació per quan un algorisme no existeix.
     * @return integer Retorna un 2
     */
    static $ALGORITHM_NOT_EXISTS_CODE = 2;

    /* Codi d'informació per quan hi ha hagut algun error amb el fitxer XML.
     * @return integer Retorna un -6
     */
    static $XML_ERROR_CODE = -6;

    /* Codi d'informació per quan un fitxer d'algorisme no s'ha pogut compilar.
     * @return integer Retorna un -7
     */
    static $UNCOMPILED_ALGORITHM_CODE = -7;

    /* Codi d'informació per quan un fitxer d'algorisme no ha estat carregat.
     * @return integer Retorna un -8
     */
    static $UNLOADED_ALGORITHM_CODE = -8;

    /* Codi d'informació per quan una comanda no estava definida.
     * @return integer Retorna un -9
     */
    static $UNDEFINED_ALGORITHM_NAME_CODE = -9;

    /* Codi d'informació per quan una comanda no estava definida.
     * @return integer Retorna un -10
     */
    static $UNDEFINED_COMMAND_CODE = -10;
//    
    //Comandas
    static $COMMAND_PARAM = "do";
    static $EXISTS_ALGORITHM_PARAM = 'existsAlgorithm';
    static $MODIFY_ALGORITHM_PARAM = 'modifyAlgorithm';
    static $APPEND_ALGORITHM_PARAM = 'appendAlgorithm';
    static $ALGORITHM_NAME_PARAM = 'algorithmName';
    //Params de l'algorisme
    static $ALGORISME_PARAM = "algorisme";
    static $ID_PARAM = "id";
    static $NOM_PARAM = "nom";
    static $DESCRIPCIO_PARAM = "descripcio";
    static $CLASSE_PARAM = "classe";
    //Parametres del fitxer
    static $FILE_PARAM = 'uploadedfile';
    static $FILE_CONTENT_PARAM = 'tmp_name';
    static $FILE_ERROR_PARAM = 'error';
    static $FILE_TYPE_PARAM = 'type';
    static $FILENAME_PARAM = "name";
    static $PDE_MIME_TYPE = 'application/octet-stream';
    static $PDE_EXTENSION = '.pde';
    static $JAVA_EXTENSION = '.java';
    static $COMMA = ",";
    static $DOT = ".";
    static $SLASH = "/";
    static $TWO_DOTS = ":";
    private $pdeManager;
    //private $xmlFileCreted = false;

    public function __construct() {
        parent::__construct();
        if (@file_exists(DOKU_INC . 'debug')) {
            $this->authenticatedUsersOnly = false;
        } else {
            $this->authenticatedUsersOnly = true;
        }
        //$this->xmlFileCreted = @file_exists(DOKU_INC . $this->getConf("processingXmlFile"));
        $this->pdeManager = new PdeManager($this);
    }

    protected function process() {
        $this->pdeManager->setParams($this->params);
        $response = self::$UNDEFINED_COMMAND_CODE;
        if (array_key_exists(self::$COMMAND_PARAM, $this->params)) {
            $do = $this->params[self::$COMMAND_PARAM];
            switch ($do) {
                case self::$EXISTS_ALGORITHM_PARAM:
                    $response = $this->pdeManager->existsAlgorithm();
                    break;
                case self::$MODIFY_ALGORITHM_PARAM:
                    $response = $this->pdeManager->modifyAlgorithm();
                    break;
                case self::$APPEND_ALGORITHM_PARAM:
                    $response = $this->pdeManager->appendAlgorithm();
                    break;
                default:
                    break;
            }
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {
        $responseCode = $response;
        $info = "";
        switch ($responseCode) {
            case self::$OK_CODE:
                $info = $this->getLang('ok');
                break;
            case self::$ALGORITHM_EXISTS_CODE:
                $info = $this->getLang('algorithmExists');
                break;
            case self::$ALGORITHM_NOT_EXISTS_CODE:
                $info = $this->getLang('algorithmNotExists');
                break;
            case self::$XML_ERROR_CODE:
                $info = $this->getLang('xmlError');
                break;
            case self::$UNCOMPILED_ALGORITHM_CODE:
                $info = $this->getLang('uncompiledAlgorithm');
                break;
            case self::$UNLOADED_ALGORITHM_CODE:
                $info = $this->getLang('unloadedAlgorithm');
                break;
            case self::$UNDEFINED_COMMAND_CODE:
                $info = $this->getLang('undefinedCommand');
                break;
            default:
                $info = $this->getLang('unexpectedError');
                break;
        }
        $ret->addCodeTypeResponse($responseCode, $info);
    }

}
