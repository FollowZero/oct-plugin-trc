<?php namespace Plus\Trc;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }
    public function register()
    {
        $this->registerConsoleCommand('trc.rg', 'Plus\Trc\Console\TrcRg');
        $this->registerConsoleCommand('trc.gj', 'Plus\Trc\Console\TrcGj');
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'TRC20',
                'description' => 'TRC20设置',
                'category'    => 'TRC20',
                'icon'        => 'oc-icon-bitcoin',
                'class'       => 'Plus\Trc\Models\Settings',
                'order'       => 600,
            ]
        ];
    }
}
