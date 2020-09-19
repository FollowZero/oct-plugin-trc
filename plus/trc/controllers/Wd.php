<?php namespace Plus\Trc\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
class Wd extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'trc_wd' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Plus.Trc', 'main-menu-item-trc20', 'side-menu-item-wd');
    }
    /**
     * 批准提币
     */
    public function preview_onPass($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);
        $model->pass();
        Flash::success('操作成功');
        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }
    /**
     * 拒绝提币
     */
    public function preview_onFail($recordId = null)
    {
        $model = $this->formFindModelObject($recordId);
        $model->fail();
        Flash::success('操作成功');
        if ($redirect = $this->makeRedirect('update-close', $model)) {
            return $redirect;
        }
    }
}
