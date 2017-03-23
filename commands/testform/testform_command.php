<?php
/**
 * Class
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");

require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'defkeys/PageKeys.php');

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
        $projectMetaData = $this->modelWrapper->getProjectMetaData($this->params);
        return $projectMetaData;
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
        $form['id'] = 'form_' . $this->params['id']; // ALERTA[Xavi]Compte, els : s'han de reemplaçar per _
        $form['method'] = 'GET'; // GET|POST
        $form['action'] = 'test.php'; // URL de destí del formulari <-- que serà una crida ajax
//        $fotm['enctype'] = 'multipart/form-data'; // ALERTA[Xavi] Opcional, només per la pujada de fitxers


        // El grid està coposat per 12 columnes
        // Si no s'especifica el nombre de columnes s'utilitzen 12
        // Les columnes es poden especificar a:
        // * Group: indica el nombre de columnes que emplea el grup
        // * Field: indica el nombre de columnes que emplea el camp. S'ha de tenir en compte que es sobre 12 INDEPENDENMENT del nombre de columnes del grup ja que són niuades

        $form['rows'] = [ // ALERTA: Per organitzar-los al frontend es més comode com array, si es fa associatiu s'ha d'afegir un diccionary amb la correspondència
            [
                'title' => 'Paràmetres de Dokuwiki',
                'groups' => [
                    [
                        'columns' => 3,
                        'hasFrame' => true,
                        'title' => 'Paràmetres bàsics', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Títol del wiki', // Etiqueta del formulari
                                'name' => 'title',
                                'value' => 'DokuIOC_josep',
                                'type' => 'text',
//                                'columns' => 12, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 1, // Més alt es més prioritari
                            ],
                            [
                                'label' => 'Nom de la pàgina d\'inici', // Etiqueta del formulari
                                'name' => 'start',
                                'value' => '',
                                'type' => 'text',
//                                'columns' => 12,
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Introdueix el nom de la pàgina d\'inici']
                            ]
                        ]
                    ],
                    [
                        'columns' => 6,
                        'hasFrame' => false,
                        'title' => 'Paràmetres de visualització', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Canvis recents', // Etiqueta del formulari
                                'name' => 'recent',
                                'type' => 'number',
                                'columns' => 6, // Això es converteix en: 12/(columns/cols) = 6 al grid de bootstrap
                                'priority' => 10, // Més alt es més prioritari
                                'value' => 'R.',
                            ],
                            [
                                'label' => 'Quantitat de canvis recents que es mantenen', // Etiqueta del formulari
                                'name' => 'recent_days',
                                'value' => '',
                                'type' => 'number',
                                'columns' => 6,
                                'priority' => 1, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Quantiat de canvis recents en dies']
                            ],
                            [
                                'label' => 'Camp d\'amplada total', // Etiqueta del formulari
                                'name' => 'amplada',
                                'value' => 'amplada completa',
                                'type' => 'input',
                                'priority' => 1, // Més alt es més prioritari
                            ]
                        ],
                    ],
                    [
                        'columns' => 3,
                        'hasFrame' => false,
//                    'title' => 'Titol del test sense frame', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Utilitza llistes de control', // Etiqueta del formulari
                                'name' => 'useacl',
//                                'value' => true,
                                'type' => 'checkbox',
                                'columns' => 6,
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['checked' => true]
                            ],
                            [
                                'label' => 'Notificacions', // Etiqueta del formulari
                                'name' => 'notifications',
//                                'value' => true,
                                'type' => 'checkbox',
                                'columns' => 6,
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['checked' => false]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Segona fila',
                'groups' => [
                    [
                        'columns' => 6,
                        'hasFrame' => true,
                        'title' => 'Amplada de columna 6 = 50%', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Camp d\'amplada màxima', // Etiqueta del formulari
                                'name' => 'surname',
                                'value' => '',
                                'type' => 'input',
                                'priority' => 10,
                                'props' => ['placeholder' => 'Introdueix el cognom']
                            ]
                        ]
                    ],
                    [
                        'columns' => 3,
                        'hasFrame' => false,
                        'title' => 'Grup d\'amplada 3 = 25%', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Mitja amplada 6 = 50%', // Etiqueta del formulari
                                'name' => 'name2',
                                'type' => 'text',
                                'columns' => 6,
                                'priority' => 1, // Més alt es més prioritari
                                'value' => 'R.',
                            ],
                            [
                                'label' => 'Mitja amplada 6 = 50%', // Etiqueta del formulari
                                'name' => 'surname2',
                                'value' => '',
                                'type' => 'input',
                                'columns' => 6,
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Introdueix el cognom']
                            ],
                            [
                                'label' => 'Amplada 12 = 100%', // Etiqueta del formulari
                                'name' => 'name',
                                'value' => 'amplada completa',
                                'type' => 'input',
                                'priority' => 99, // Més alt es més prioritari
                            ]
                        ],
                    ],
                    [
                        'columns' => 3,
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Nom3', // Etiqueta del formulari
                                'value' => 'res especial',
                                'type' => 'input',
                                'priority' => 10, // Més alt es més prioritari
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Demostració controls afegits',
                'groups' => [
                    [
                        'columns' => 6,
                        'hasFrame' => true,
                        'title' => 'check/radius', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Checkbox1', // Etiqueta del formulari
                                'name' => 'check2',
                                'columns' => 2,
                                'type' => 'checkbox',
                                'props' => ['checked' => true]
                            ],
                            [
                                'label' => 'Checkbox2', // Etiqueta del formulari
                                'name' => 'check1',
                                'columns' => 2,
                                'type' => 'checkbox',
                            ],
                            [
                                'label' => 'Radius1 (grp1)', // Etiqueta del formulari
                                'name' => 'radius-group-1',
                                'value' => 1,
                                'columns' => 2,
                                'type' => 'radio',
                                'props' => ['checked' => true]
                            ],
                            [
                                'label' => 'Radius2 (grp1)', // Etiqueta del formulari
                                'value' => 2,
                                'name' => 'radius-group-1',
                                'columns' => 2,
                                'type' => 'radio',
                            ],
                            [
                                'label' => 'Radius3 (grp2)', // Etiqueta del formulari
                                'name' => 'radius-group-2',
                                'value' => 3,
                                'columns' => 2,
                                'type' => 'radio',
                            ],
                            [
                                'label' => 'Radius4 (grp4)', // Etiqueta del formulari
                                'value' => 4,
                                'name' => 'radius-group-2',
                                'columns' => 2,
                                'type' => 'radio',
                                'props' => ['checked' => true]
                            ]


                        ]
                    ],
                    [
                        'columns' => 3,
                        'hasFrame' => false,
                        'title' => 'Grup d\'amplada 3 = 25%', // Optatiu
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Mitja amplada 6 = 50%', // Etiqueta del formulari
                                'name' => 'name2',
                                'type' => 'text',
                                'columns' => 6,
                                'priority' => 10, // Més alt es més prioritari
                                'value' => 'R.',
                            ],
                            [
                                'label' => 'Mitja amplada 6 = 50%', // Etiqueta del formulari
                                'name' => 'surname2',
                                'value' => '',
                                'type' => 'input',
                                'columns' => 6,
                                'priority' => 1, // Més alt es més prioritari
                                'props' => ['placeholder' => 'Introdueix el cognom']
                            ],
                            [
                                'label' => 'Amplada completa ', // Etiqueta del formulari
                                'name' => 'name',
                                'value' => 'amplada completa',
                                'type' => 'input',
                                'priority' => 1, // Més alt es més prioritari
                            ]
                        ],
                    ],
                    [
                        'columns' => 3,
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Nom3', // Etiqueta del formulari
                                'value' => 'res especial',
                                'type' => 'input',
                                'priority' => 10, // Més alt es més prioritari
                            ]
                        ]
                    ],
                    [
                        'title' => 'Demostració textarea',
                        'hasFrame' => true,
//                        'columns' => 12,
                        'priority' => 10, // Més alt es més prioritari
                        'fields' => [
                            [
                                'label' => 'Nom3', // Etiqueta del formulari
                                'value' => 'Contingut del textarea',
                                'name' => 'demotextarea',
                                'type' => 'textarea',
                                'priority' => 10, // Més alt es més prioritari
                                'props' => ['rows' => 5]
                            ],
                            [
                                'label' => 'Demostració select',
                                'type' => 'select',
                                'name' => 'demoselect',
                                'columns' => 3,
                                'options' => [
                                    ['value' => 'B', 'description' => 'Barcelona'],
                                    ['value' => 'T', 'description' => 'Tarragona'],
                                    ['value' => 'L', 'description' => 'Lleida', 'selected' => true],
                                    ['value' => 'G', 'description' => 'Girona']
                                ]
                            ]

                        ]

                    ],


                ]
            ]

        ];





        if ($response['info']) {
            $ret->addInfoDta($response['info']);
        }

        $id = $this->params['id']; // Alerta[Xavi] només és una prova, però s'ha de comptar que no es reemplcen els : si es fa servir una carpeta
        $ns = $this->params['id'];
//        $title = "Formulari TestForm";
        $title = $response['test'];

        $ret->addForm($id, $ns, $title, $form);
    }

    public function getAuthorizationType()
    {
        return "_none";
    }

}
