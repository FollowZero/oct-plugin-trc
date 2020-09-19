<?php namespace Plus\Trc\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Rg extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController'    ];
    
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = [
        'trc_rg' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Plus.Trc', 'main-menu-item-trc20', 'side-menu-item-rg');
    }
}
