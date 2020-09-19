<?php namespace Plus\Trc\Models;

use IEXBase\TronAPI\Tron;
use Illuminate\Support\Facades\Crypt;
use Input;
use Model;

/**
 * Model
 */
class CurrencyModel extends Model
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
    public $table = 'plus_trc_currency';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    public $attachOne = [
        'icon' => 'System\Models\File',
    ];
    public $belongsTo= [
        'node' => ['Plus\Trc\Models\NodeModel'],
    ];
    public function beforeSave(){
        if ($this->main_pwd != $this->original['main_pwd']) {
            //当私钥有修改的时候，加密保存
            $this->main_pwd=Crypt::encryptString($this->main_pwd);
        }
        //hex格式的地址
        $tron=new Tron();
        $this->main_hex=$tron->toHex($this->main);
    }
}
