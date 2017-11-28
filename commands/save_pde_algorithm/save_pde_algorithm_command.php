<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND_PDE')) define('DOKU_COMMAND_PDE', DOKU_INC . "lib/plugins/ajaxcommand/commands/save_pde_algorithm/");
require_once (DOKU_COMMAND_PDE . 'PdeManager.php');

/**
 * save_pde_algorithm_command
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
class save_pde_algorithm_command extends abstract_command_class {
    const OK_CODE = 0;                    //indica que tot ha anat correctament
    const ALGORITHM_EXISTS_CODE = -2;     //indica que un algorisme ja existeix
    const ALGORITHM_NOT_EXISTS_CODE = 2;  //indica que un algorisme no existeix
    const XML_ERROR_CODE = -6;            //indica que hi ha hagut algun error amb el fitxer XML
    const UNCOMPILED_ALGORITHM_CODE = -7; //indica que un fitxer d'algorisme no s'ha pogut compilar
    const UNLOADED_ALGORITHM_CODE = -8;   //indica que un fitxer d'algorisme no ha estat carregat
    const UNDEFINED_ALGORITHM_NAME_CODE = -9; //indica que una comanda no estava definida
    const UNDEFINED_COMMAND_CODE = -10;   //indica que una comanda no estava definida
    //Comandas
    const COMMAND_PARAM = "do";
    const EXISTS_ALGORITHM_PARAM = "existsAlgorithm";
    const MODIFY_ALGORITHM_PARAM = "modifyAlgorithm";
    const APPEND_ALGORITHM_PARAM = "appendAlgorithm";
    const ALGORITHM_NAME_PARAM   = "algorithmName";
    //Params de l'algorisme
    const ALGORISME_PARAM = "algorisme";
    const ID_PARAM = "id";
    const NOM_PARAM = "nom";
    const DESCRIPCIO_PARAM = "descripcio";
    const CLASSE_PARAM = "classe";
    //Parametres del fitxer
    const FILE_PARAM = 'uploadedfile';
    const FILE_CONTENT_PARAM = 'tmp_name';
    const FILE_ERROR_PARAM = 'error';
    const FILE_TYPE_PARAM = 'type';
    const FILENAME_PARAM = "name";
    const PDE_MIME_TYPE = 'application/octet-stream';
    const PDE_EXTENSION = '.pde';
    const JAVA_EXTENSION = '.java';
    const COMMA = ",";
    const DOT = ".";
    const SLASH = "/";
    const TWO_DOTS = ":";

    private $pdeManager;

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
        $response = self::UNDEFINED_COMMAND_CODE;
        if (array_key_exists(self::COMMAND_PARAM, $this->params)) {
            $do = $this->params[self::COMMAND_PARAM];
            switch ($do) {
                case self::EXISTS_ALGORITHM_PARAM:
                    $response = $this->pdeManager->existsAlgorithm();
                    break;
                case self::MODIFY_ALGORITHM_PARAM:
                    $response = $this->pdeManager->modifyAlgorithm();
                    break;
                case self::APPEND_ALGORITHM_PARAM:
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
            case self::OK_CODE:
                $info = $this->getLang('ok');
                break;
            case self::ALGORITHM_EXISTS_CODE:
                $info = $this->getLang('algorithmExists');
                break;
            case self::ALGORITHM_NOT_EXISTS_CODE:
                $info = $this->getLang('algorithmNotExists');
                break;
            case self::XML_ERROR_CODE:
                $info = $this->getLang('xmlError');
                break;
            case self::UNCOMPILED_ALGORITHM_CODE:
                $info = $this->getLang('uncompiledAlgorithm');
                break;
            case self::UNLOADED_ALGORITHM_CODE:
                $info = $this->getLang('unloadedAlgorithm');
                break;
            case self::UNDEFINED_COMMAND_CODE:
                $info = $this->getLang('undefinedCommand');
                break;
            default:
                $info = $this->getLang('unexpectedError');
                break;
        }
        $ret->addCodeTypeResponse($responseCode, $info);
    }

}
