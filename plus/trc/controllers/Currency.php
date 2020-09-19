<?php namespace Plus\Trc\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Currency extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'trc_currency' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Plus.Trc', 'main-menu-item-trc20', 'side-menu-item-currency');
    }
}
