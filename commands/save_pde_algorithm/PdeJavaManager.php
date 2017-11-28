<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND_PDE')) define('DOKU_COMMAND_PDE', DOKU_INC . "lib/plugins/ajaxcommand/commands/save_pde_algorithm/");
require_once (DOKU_COMMAND_PDE . 'PdeXmlManager.php');

/**
 * PdeJavaManager
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
class PdeJavaManager {

    private $command;
    private $params;
    
    function PdeJavaManager($command) {
        $this->command = $command;
    }

    function setParams($params) {
        $this->params = $params;
    }

    /**
     * Genera i compila l'algorisme Pde.
     * @param type $className Nom de la classe Java
     * @param type $pdePath path del fitxer Pde
     * @return bool True on success, False otherwise
     */
    function generateJavaClass($className, $pdePath) {
        $command = "java -cp " . $this->getJavaDir()
                . "ParsePdeAlgorithm.jar ioc.parsepdealgorithm.PdeToIocImageGenerator"
                . " -pkg=ioc.wiki.processingmanager -cn=" . $className
                . " -pde=" . $pdePath . " -outd=" . $this->getSrcRepositoryDir();

        exec($command, $output, $returnVar);
        $generated = $returnVar == 0;
        if ($generated) {
            $javaPath = $this->getSrcRepositoryDir() . $this->command->getConf('processingPackage') . $className . save_pde_algorithm_command::JAVA_EXTENSION;
            $generated = $this->compileSource($javaPath);
        }
        if (!$generated) {
            unlink($javaPath); //Eliminar source en cas de fallada de compilació
        }
        return $generated;
    }

    /**
     * Genera el fitxer .class compilant el fitxer .java
     * @param type $javaPath path del fitxer Java a compilar
     * @return bool True on succes, False otherwise
     */
    private function compileSource($javaPath) {
        $pathClasses = $this->getClassesRepositoryDir();
        $pathLibs = $this->getJavaLibDir();
        $libNames = $this->command->getConf('javaLibs');
        $arrayLibs = split(save_pde_algorithm_command::COMMA, $libNames);
        $libs = "";
        foreach ($arrayLibs as $lib) {
            $libs .= $pathLibs . $lib . save_pde_algorithm_command::TWO_DOTS;
        }
        $libs = substr($libs, 0, -1); //Treu els ultims dos punts que sobren.
        //Comanda que funciona
        //javac -classpath ../../../../classes/:../../../../../lib/core.jar IdDani.java
        $command = "javac -d " . $pathClasses . " -classpath " . $libs . " " . $javaPath;
        exec($command, $output, $returnVar);
        return $returnVar == 0;
    }

    private function getJavaLibDir() {
        return DOKU_INC . $this->command->getConf('javaLibDir');
    }

    private function getJavaDir() {
        return DOKU_INC . $this->command->getConf('javaDir');
    }

        /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getSrcRepositoryDir() {
        return DOKU_INC . $this->command->getConf('processingSrcRepository');
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getClassesRepositoryDir() {
        return DOKU_INC . $this->command->getConf('processingClassesRepository');
    }
}

