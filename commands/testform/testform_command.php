<?php
if (!defined('DOKU_INC')) {
    die();
}
if (!defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
}
if (!defined('DOKU_COMMAND')) {
    define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
}
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'requestparams/PageKeys.php');

/**
 * Class edit_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class testform_command extends abstract_command_class
{

    /**
     * Al constructor s'estableixen els tipus, els valors per defecte, i s'estableixen aquest valors com a paràmetres.
     */
    public function __construct()
    {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = abstract_command_class::T_STRING;

    }

    /**
     * Retorna el contingut de la página segons els paràmetres emmagatzemats en aquest command.
     *
     * @return array amb el contingut de la pàgina (id, ns, tittle i content)
     */
    protected function process()
    {

        return [];
    }

    /**
     * Afegeix la pàgina passada com argument com una resposta de tipus DATA_TYPE al generador de respostes.
     *
     * @param array $response amb el contingut de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     *
     * @return mixed|void
     */
    protected function getDefaultResponse($response, &$ret)
    {
        $form = [];
//        $form['view'] = [
//            'rows' => 10, // Aquest no crec que sigui necessari
//            'columns' => 4 // Ha de ser divisor de 12: Bootstrap far servir un grid de 12 que s'ha de dividir per aquest nombre
//        ];
        $form['method'] = 'GET'; // GET|POST
        $form['action'] = '#';
        $fotm['enctype'] = 'alguna cosa aixi';

        $form['rows'] = [ // ALERTA: Per organitzar-los al frontend es més comode com array, si es fa associatiu s'ha d'afegir un diccionary amb la correspondència
            [
                'columns' => 4,
                'title' => 'Paràmetres de Dokuwiki',
                'groups' => [
                    [
                        'hasFrame' => true,
                        'title' => 'Paràmetres bàsics', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Títol del wiki', // Etiqueta del formulari
                                'name' => 'title',
                                'value' => 'DokuIOC_josep',
                                'type' => 'text',
                                'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 1, // Més alt es més prioritari
                            ],
                            [
                                'label' => 'Nom de la pàgina d\'inici', // Etiqueta del formulari
                                'name' => 'start',
                                'value' => '',
                                'type' => 'text',
                                'cols' => 2,
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Introdueix el nom de la pàgina d\'inici']
                            ]
                        ]
                    ],
                    [
                        'hasFrame' => false,
                        'title' => 'Paràmetres de visualització', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Canvis recents', // Etiqueta del formulari
                                'name' => 'recent',
                                'type' => 'number',
                                'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 10, // Més alt es més prioritari
                                'value' => 'R.',
                            ],
                            [
                                'label' => 'Quantitat de canvis recents que es mantenen', // Etiqueta del formulari
                                'name' => 'recent_days',
                                'value' => '',
                                'type' => 'number',
                                'cols' => 2,
                                'priority' => 1, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Quantiat de canvis recents en dies']
                            ]
                        ],
                    ],
                    [
                        'hasFrame' => false,
//                    'title' => 'Titol del test sense frame', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Utilitza llistes de control', // Etiqueta del formulari
                                'name' => 'useacl',
//                                'value' => true,
                                'type' => 'checkbox',
                                'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['checked' => true]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'columns' => 2,
                'title' => 'Fila dos',
                'groups' => [
                    [
                        'hasFrame' => true,
                        'title' => 'Títol del testform', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Cognom', // Etiqueta del formulari
                                'name' => 'surname',
                                'value' => '',
                                'type' => 'input',
                                'cols' => 2,
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Introdueix el cognom']
                            ]
                        ]
                    ],
                    [
                        'hasFrame' => false,
                        'title' => 'Titol del test sense frame', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Nom2', // Etiqueta del formulari
                                'name' => 'name2',
                                'type' => 'input',
                                'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 10, // Més alt es més prioritari
                                'value' => 'R.',
                            ],
                            [
                                'label' => 'Cognom2', // Etiqueta del formulari
                                'name' => 'surname2',
                                'value' => '',
                                'type' => 'input',
                                'cols' => 2,
                                'priority' => 1, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Introdueix el cognom']
                            ],
                            [
                                'label' => 'Nom3', // Etiqueta del formulari
                                'name' => 'name',
                                'value' => 'Rickirin',
                                'type' => 'input',
                                'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 1, // Més alt es més prioritari
                            ]
                        ],
                    ],
                    [
                        'hasFrame' => false,
//                    'title' => 'Titol del test sense frame', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Nom3', // Etiqueta del formulari
                                'value' => 'Richie',
                                'type' => 'input',
                                'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 10, // Més alt es més prioritari
                            ]
                        ]
                    ]
                ]
            ]

        ];


//        $form['groups'] = [ // ALERTA[Xavi] Canviar el array per un associatiu?
//            [
//                'hasFrame' => true,
//                'title' => 'Títol del testform', // Optatiu
//                'priority' => 10, // Més alt es més prioritari
//                'fields' => [
//                    [
//                        'label' => 'Nom', // Etiqueta del formulari
//                        'name' => 'name',
//                        'value' => 'Rick',
//                        'type' => 'input',
//                        'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
//                        'priority' => 1, // Més alt es més prioritari
//                    ],
//                    [
//                        'label' => 'Cognom', // Etiqueta del formulari
//                        'name' => 'surname',
//                        'value' => '',
//                        'type' => 'input',
//                        'cols' => 2,
//                        'priority' => 10, // Més alt es més prioritari
//                        'props' => ['placeholder' => 'Introdueix el cognom']
//                    ]
//                ]
//            ],
//            [
//                'hasFrame' => false,
//                'title' => 'Titol del test sense frame', // Optatiu
//                'priority' => 10, // Més alt es més prioritari
//                'fields' => [
//                    [
//                        'label' => 'Nom2', // Etiqueta del formulari
//                        'name' => 'name2',
//                        'type' => 'input',
//                        'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
//                        'priority' => 10, // Més alt es més prioritari
//                        'value' => 'R.',
//                    ],
//                    [
//                        'label' => 'Cognom2', // Etiqueta del formulari
//                        'name' => 'surname2',
//                        'value' => '',
//                        'type' => 'input',
//                        'cols' => 2,
//                        'priority' => 1, // Més alt es més prioritari
//                        'props' => ['placeholder' => 'Introdueix el cognom']
//                    ]
//                ],
//            ],
//            [
//                'hasFrame' => false,
////                    'title' => 'Titol del test sense frame', // Optatiu
//                'priority' => 10, // Més alt es més prioritari
//                'fields' => [
//                    [
//                        'label' => 'Nom3', // Etiqueta del formulari
//                        'value' => 'Richie',
//                        'type' => 'input',
//                        'cols' => 2, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
//                        'priority' => 10, // Més alt es més prioritari
//                    ]
//                ]
//            ]
//        ];


        if ($response['info']) {
            $ret->addInfoDta($response['info']);
        }

        $id = $this->params['id']; // Alerta[Xavi] només és una prova, però s'ha de comptar que no es reemplcen els : si es fa servir una carpeta
        $ns = $this->params['id'];
        $title = "Formulari TestForm";

        $ret->addForm($id, $ns, $title, $form);
    }

    public function getAuthorizationType()
    {
        return "_none";
    }

}
