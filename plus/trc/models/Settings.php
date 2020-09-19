<?php namespace Plus\Trc\Models;

use Model;

/**
 * Model
 */
class Settings extends Model
{


    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'trc_settings';
    public $settingsFields = 'fields.yaml';
}
