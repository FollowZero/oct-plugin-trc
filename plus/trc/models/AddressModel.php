<?php namespace Plus\Trc\Models;

use IEXBase\TronAPI\Tron;
use Illuminate\Support\Facades\Crypt;
use Model;

/**
 * Model
 */
class AddressModel extends Model
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
    public $table = 'plus_trc_address';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    public function beforeCreate(){
        //生成地址时,加密保存
        $this->privatekey=Crypt::encryptString($this->privatekey);
    }
}
