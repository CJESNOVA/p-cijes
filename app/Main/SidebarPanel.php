<?php

namespace App\Main;

use Illuminate\Support\Facades\Auth;

class SidebarPanel
{

    public static function membreType()
    {
        $user = Auth::user();

        if (!$user || !$user->membre) {
            return null; // utilisateur non connecté ou pas encore membre
        }

        switch ($user->membre->membretype_id) {
            case 1:
                return 'incube';
            case 2:
                return 'expert';
            case 3:
                return 'conseille';
            default:
                return null;
        }

    }


    public static function membres()
    {
        return [
            'title' => 'Membres',
            'items' => [
                [
                    'profil' => [
                        'title' => 'Mon profil',
                        'route_name' => 'membre.createOrEdit'
                    ],
                    'document' => [
                        'title' => 'Mes documents',
                        'route_name' => 'documents.form'
                    ],
                    'parrainage' => [
                        'title' => 'Parrainage',
                        'route_name' => 'parrainage.index'
                    ],
                ],
            ]
        ];
    }




    public static function entreprises()
    {
        $type = self::membreType();

        if ($type !== 'conseille') {

        return [
            'title' => 'Entreprises',
            'items' => [
                [
                    'entreprise' => [
                        'title' => 'Entreprises',
                        'route_name' => 'entreprise.index'
                    ],
                    'piece' => [
                        'title' => 'Mes pièces administratives',
                        'route_name' => 'pieces.form'
                    ],
                    'cotisation' => [
                        'title' => 'Mes cotisations',
                        'route_name' => 'cotisation.index'
                    ],
                    'cotisationressource' => [
                        'title' => 'Cotisations payées',
                        'route_name' => 'cotisationressource.index'
                    ],
                ],
            ]
        ];

        }
    }










    public static function diagnostics()
    {
        $type = self::membreType();

        if ($type !== 'conseille') {

        return [
            'title' => 'Diagnostics',
            'items' => [
                [
                    'diagnostic' => [
                        'title' => 'Tests psychotechniques',
                        'route_name' => 'diagnostic.form'
                    ],
                    'diagnosticentreprise' => [
                        'title' => 'Niveaux de structuration',
                        'route_name' => 'diagnosticentreprise.indexForm'
                    ],
                ],
            ]
        ];

        }
    }
    
    




    




    public static function accompagnements()
    {
        $type = self::membreType();

        if ($type !== 'conseille') {

        return [
            'title' => 'Accompagnements',
            'items' => [
                [
                    'accompagnement' => [
                        'title' => 'Mes accompagnements',
                        'route_name' => 'accompagnement.mes'
                    ],
                    'plan' => [
                        'title' => 'Mes plans d\'accompagnements',
                        'route_name' => 'plan.index'
                    ],
                ],
            ]
        ];

        }
    }











    public static function evenements()
    {
        return [
            'title' => 'Événements',
            'items' => [
                [
                    'evenement' => [
                        'title' => 'Événements à venir',
                        'route_name' => 'evenement.index'
                    ],
                    'evenementressource' => [
                        'title' => 'Evenements payées',
                        'route_name' => 'evenementressource.index'
                    ],
                    'espace' => [
                        'title' => 'Espaces physiques',
                        'route_name' => 'espace.index'
                    ],
                    'reservation' => [
                        'title' => 'Mes réservations',
                        'route_name' => 'reservation.index'
                    ],
                    'espaceressource' => [
                        'title' => 'Espaces payées',
                        'route_name' => 'espaceressource.index'
                    ],
                ],
            ]
        ];
        
    }






    




    public static function experts()
    {
        $type = self::membreType();

        if ($type === 'incube') {

        return [
            'title' => 'Experts & conseillers',
            'items' => [
                [
                    'expertliste' => [
                        'title' => 'Experts disponibles',
                        'route_name' => 'expert.liste'
                    ],
                    'conseiller' => [
                        'title' => 'Mes conseillers',
                        'route_name' => 'conseiller.mes_conseillers'
                    ],
                    'propositions' => [
                        'title' => 'Propositions reçues',
                        'route_name' => 'proposition.membre.index'
                    ],
                ],
            ]
        ];

        } else if ($type === 'expert') {

        return [
            'title' => 'Experts & conseillers',
            'items' => [
                [
                    'expert' => [
                        'title' => 'Mes profils experts',
                        'route_name' => 'expert.index'
                    ],
                    'disponibilite' => [
                        'title' => 'Disponibilité',
                        'route_name' => 'disponibilite.create'
                    ],
                    'expertliste' => [
                        'title' => 'Experts disponibles',
                        'route_name' => 'expert.liste'
                    ],
                    'plans' => [
                        'title' => 'Plans disponibles',
                        'route_name' => 'expert.plans.index'
                    ],
                    'propositions' => [
                        'title' => 'Mes propositions',
                        'route_name' => 'proposition.index'
                    ],
                ],
            ]
        ];

        } else if ($type === 'conseille') {

        return [
            'title' => 'Experts & conseillers',
            'items' => [
                [
                    'expert' => [
                        'title' => 'Mes profils experts',
                        'route_name' => 'expert.index'
                    ],
                    'disponibilite' => [
                        'title' => 'Disponibilité',
                        'route_name' => 'disponibilite.create'
                    ],
                    'expertliste' => [
                        'title' => 'Experts disponibles',
                        'route_name' => 'expert.liste'
                    ],
                    'conseiller' => [
                        'title' => 'Mes conseillers',
                        'route_name' => 'conseiller.mes_conseillers'
                    ],
                    'prescription' => [
                        'title' => 'Mes prescriptions',
                        'route_name' => 'conseiller.index'
                    ],
                    'propositions' => [
                        'title' => 'Propositions reçues',
                        'route_name' => 'proposition.membre.index'
                    ],
                ],
            ]
        ];

        } else {
        return null;
        }

    }











    public static function prestations()
    {
        $type = self::membreType();

        if ($type === 'incube') {
            
        return [
            'title' => 'Prestations & formations',
            'items' => [
                [
                    'prestationliste' => [
                        'title' => 'Prestations disponibles',
                        'route_name' => 'prestation.liste'
                    ],
                    'prestationressource' => [
                        'title' => 'Prestations payées',
                        'route_name' => 'prestationressource.index'
                    ],
                    'participant' => [
                        'title' => 'Les formations ouvertes',
                        'route_name' => 'formation.liste'
                    ],
                    'formationressource' => [
                        'title' => 'Formations payées',
                        'route_name' => 'formationressource.index'
                    ],
                ],
            ]
        ];

        } else if ($type === 'expert') {
            
        return [
            'title' => 'Prestations & formations',
            'items' => [
                [
                    'prestation' => [
                        'title' => 'Prestations',
                        'route_name' => 'prestation.index'
                    ],
                    'prestationliste' => [
                        'title' => 'Prestations disponibles',
                        'route_name' => 'prestation.liste'
                    ],
                    'prestationressource' => [
                        'title' => 'Prestations payées',
                        'route_name' => 'prestationressource.index'
                    ],
                    'formation' => [
                        'title' => 'Formations',
                        'route_name' => 'formation.index'
                    ],
                    'quiz' => [
                        'title' => 'Mes quiz',
                        'route_name' => 'quiz.index'
                    ],
                    'participant' => [
                        'title' => 'Les formations ouvertes',
                        'route_name' => 'formation.liste'
                    ],
                    'formationressource' => [
                        'title' => 'Formations payées',
                        'route_name' => 'formationressource.index'
                    ],
                ],
            ]
        ];

        } else if ($type === 'conseille') {
            
        return [
            'title' => 'Prestations & formations',
            'items' => [
                [
                    'prestation' => [
                        'title' => 'Prestations',
                        'route_name' => 'prestation.index'
                    ],
                    'prestationliste' => [
                        'title' => 'Prestations disponibles',
                        'route_name' => 'prestation.liste'
                    ],
                    'prestationressource' => [
                        'title' => 'Prestations payées',
                        'route_name' => 'prestationressource.index'
                    ],
                    'formation' => [
                        'title' => 'Formations',
                        'route_name' => 'formation.index'
                    ],
                    'quiz' => [
                        'title' => 'Mes quiz',
                        'route_name' => 'quiz.index'
                    ],
                    'participant' => [
                        'title' => 'Les formations ouvertes',
                        'route_name' => 'formation.liste'
                    ],
                    'formationressource' => [
                        'title' => 'Formations payées',
                        'route_name' => 'formationressource.index'
                    ],
                ],
            ]
        ];

        } else {   
        return null;
        }
        
    }











    public static function bons()
    {
        return [
            'title' => 'Ressources',
            'items' => [
                [
                    'ressource' => [
                        'title' => 'Mes ressources',
                        'route_name' => 'ressourcecompte.index'
                    ],
                    'conversion' => [
                        'title' => 'Mes conversions',
                        'route_name' => 'conversion.index'
                    ],
                    'recompense' => [
                        'title' => 'Mes récompenses',
                        'route_name' => 'recompense.mesRecompenses'
                    ],
                    /*'bon' => [
                        'title' => 'Mes bons disponibles',
                        'route_name' => 'bon.index'
                    ],
                    'credit' => [
                        'title' => 'Mes crédits disponibles',
                        'route_name' => 'credit.index'
                    ],*/
                ],
            ]
        ];
    }



    public static function forums()
    {
        return [
            'title' => 'Forums',
            'items' => [
                [
                    'forum' => [
                        'title' => 'Mes sujets',
                        'route_name' => 'forum.index'
                    ],
                    'sujet' => [
                        'title' => 'Sujets de disccussion',
                        'route_name' => 'sujet.liste'
                    ],
                    'message' => [
                        'title' => 'Messagerie',
                        'route_name' => 'message.index'
                    ],
                ],
            ]
        ];
    }




    public static function dashboards()
    {
        return [
            'title' => 'Dashboards',
            'items' => [
                [
                    'dashboards_crm_analytics' => [
                        'title' => 'CRM Analytics',
                        'route_name' => 'dashboards/crm-analytics'
                    ],
                    'dashboards_orders' => [
                        'title' => 'Orders',
                        'route_name' => 'dashboards/orders'
                    ],
                ],
                [
                    'dashboards_crypto' => [
                        'title' => 'Cryptocurrency',
                        'submenu' => [
                            'dashboards_crypto_1' => [
                                'title' => 'Cryptocurrency v1',
                                'route_name' => 'dashboards/crypto-1'
                            ],
                            'dashboards_crypto_2' => [
                                'title' => 'Cryptocurrency v2',
                                'route_name' => 'dashboards/crypto-2'
                            ]
                        ]
                    ],
                    'dashboards_banking' => [
                        'title' => 'Banking',
                        'submenu' => [
                            'dashboards_banking_1' => [
                                'title' => 'Banking v1',
                                'route_name' => 'dashboards/banking-1'
                            ],
                            'dashboards_banking_2' => [
                                'title' => 'Banking v2',
                                'route_name' => 'dashboards/banking-2'
                            ]
                        ]
                    ],
                    'dashboards_personal' => [
                        'title' => 'Personal',
                        'route_name' => 'dashboards/personal'
                    ],
                    'dashboards_cms_analytics' => [
                        'title' => 'CMS Analytics',
                        'route_name' => 'dashboards/cms-analytics'
                    ],
                    'dashboards_influencer' => [
                        'title' => 'Influencer',
                        'route_name' => 'dashboards/influencer'
                    ],
                    'dashboards_travel' => [
                        'title' => 'Travel',
                        'route_name' => 'dashboards/travel'
                    ],
                    'dashboards_teacher' => [
                        'title' => 'Teacher',
                        'route_name' => 'dashboards/teacher'
                    ],
                    'dashboards_education' => [
                        'title' => 'Education',
                        'route_name' => 'dashboards/education'
                    ],
                    'dashboards_authors' => [
                        'title' => 'Authors',
                        'route_name' => 'dashboards/authors'
                    ],
                    'dashboards_doctor' => [
                        'title' => 'Doctor',
                        'route_name' => 'dashboards/doctor'
                    ],
                    'dashboards_employees' => [
                        'title' => 'Employees',
                        'route_name' => 'dashboards/employees'
                    ],
                    'dashboards_workspaces' => [
                        'title' => 'Workspaces',
                        'route_name' => 'dashboards/workspaces'
                    ],
                    'dashboards_meetings' => [
                        'title' => 'Meetings',
                        'route_name' => 'dashboards/meetings'
                    ],
                    'dashboards_project_boards' => [
                        'title' => 'Project Boards',
                        'route_name' => 'dashboards/project-boards'
                    ],
                ],
                [
                    'dashboards_widget_ui' => [
                        'title' => 'Widget UI',
                        'route_name' => 'dashboards/widget-ui'
                    ],
                    'dashboards_widget_contacts' => [
                        'title' => 'Widget Contacts',
                        'route_name' => 'dashboards/widget-contacts'
                    ],
                ],
            ]
        ];
    }


    public static function all(){
        return [
            self::membres(), 
            self::entreprises(), 
            self::diagnostics(), 
            self::accompagnements(), 
            self::evenements(), 
            self::experts(), 
            self::prestations(), 
            self::bons(), 
            self::forums(), 
            self::dashboards()
        ];
    }
}
