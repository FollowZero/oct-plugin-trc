<?php namespace Plus\Trc\Models;

use Model;

/**
 * Model
 */
class RgModel extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
//    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'plus_trc_rg';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
