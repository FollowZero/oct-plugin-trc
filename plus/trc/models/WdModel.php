<?php namespace Plus\Trc\Models;

use Model;
use Plus\Trc\Classes\TrcBonus;

/**
 * Model
 */
class WdModel extends Model
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
    public $table = 'plus_trc_wd';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * 批准提币
     */
    public function pass()
    {
        if($this->status==0){
            //trc转账
            $trc_bonus=new TrcBonus();
            $res=$trc_bonus->transfer($this->address_to,$this->num_true,$this->trc_cid);
            if($res['result']){
                $this->txid= $res['txid'];
                $this->status=1;
                $this->save();
            }
        }
    }

    /**
     * 拒绝提币
     */
    public function fail()
    {
        //拒绝提币返还。看看是写在控制器还是模型里
        if($this->status==0){
            $this->status=-1;
            $this->save();
        }
    }
}
