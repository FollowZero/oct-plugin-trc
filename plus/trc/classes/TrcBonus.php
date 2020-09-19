<?php namespace Plus\Trc\Classes;

use Plus\Trc\Models\AddressModel;
use Plus\Trc\Models\CurrencyModel;
use Plus\Trc\Models\RgModel;
use Db;
class TrcBonus
{
    /**
     * 转账，提币操作
     * @param string $toaddr
     * @param float $num
     * @param $currencyid
     */
    public function transfer(string $toadd,float $num,$trc_cid){
        //TRC代币信息,获取节点
        $trc_currency=CurrencyModel::with(['node'])->where('id',$trc_cid)->where('status',1)->first();
        $node=$trc_currency->node->api??'';
        $new_address=$trc_currency->main??'';
        $new_key=$trc_currency->main_pwd??'';
        if(!$node){
            return false;
        }
        //初始化
        $tron=new TRC20($node,$new_address,$new_key);
        $res=$tron ->transferToToken($trc_currency->contract,$toadd,$num);
        return $res;
    }

    /**
     * 归集console入口
     */
    public function gj(){
        $trc_currencies=CurrencyModel::with(['node'])->where('status',1)->get();
        if($trc_currencies){
            foreach ($trc_currencies as $tcv){
                $this->doGj($tcv);
            }
        }
    }

    /**
     * 单个币种归集
     * @param $tcv
     * @return bool
     */
    public function doGj($tcv)
    {
        $node = $tcv->node->api ?? '';
        $new_address = $tcv->main ?? '';
        $new_key = $tcv->main_pwd ?? '';
        if (!$node) {
            return false;
        }
        $balance_field = 'balance_' . $tcv->name;
        $guiji_field = 'guiji_' . $tcv->name;
        //初始化
        $tron = new TRC20($node, $new_address, $new_key);
        $add_list=AddressModel::where($guiji_field,1)->get();
        if(!$add_list){
            return false;
        }
        foreach ($add_list as $alv){
            //trx余额
            $b_t=$tron->getAccount($alv->address);
            //手续费转0.3
            $need=0.3;
            if($b_t<$need){
               //转手续费
                $tron->trxTransaction($alv->address,$need);
                continue;
            }
            //归集
            $userTron = new TRC20($node,$alv->address,$alv->privatekey);
            $userTron ->transferToToken($tcv->contract,$new_address,$alv->$balance_field);
            AddressModel::where('id',$alv->id)->update(["$balance_field"=>0,"$guiji_field"=>0]);
        }
    }

    /**
     * 充币console入口
     */
    public function rg(){
        $trc_currencies=CurrencyModel::with(['node'])->where('status',1)->get();
        if($trc_currencies){
            foreach ($trc_currencies as $tcv){
                $this->doRg($tcv);
            }
        }
    }
    /**
     *  单个币种操作
     * @param $tcv
     */
    public function doRg($tcv){
        $node=$tcv->node->api??'';
        $new_address=$tcv->main??'';
        $new_key=$tcv->main_pwd??'';
        if(!$node){
            return false;
        }
        $balance_field='balance_'.$tcv->name;
        $guiji_field='guiji_'.$tcv->name;
        //初始化
        $tron=new TRC20($node,$new_address,$new_key);
        $add_list=AddressModel::all();
        if(!$add_list){
            return false;
        }
        foreach ($add_list as $alv){
            //代币精度的余额$b_d:balance_daibi
            $b_d = $tron->getAccountToToken($tcv->contract,$alv->address,$tcv->decimals);
            //数据库精度的余额.比如数据库的精度$dec_m=4;$b_m:balance_mysql
            $dec_m=4;
            $b_m = $tron->fromTronExt($tron->toTronExt($b_d,$dec_m),$dec_m);
            if ($b_m>$alv->$balance_field){
                //本次实际充值金额,要减去系统地址上未归集的数量$b_t:balance_ture
                $b_t = $b_m-$alv->$balance_field;
                //增加充值记录
                Db::beginTransaction();
                $table_rg=new RgModel();
                $table_rg->user_id=$alv->user_id;
                $table_rg->trc_cid=$tcv->id;
                $table_rg->hjd_cid=$tcv->rg_cid;
                $table_rg->address_from=$tcv->main;
                $table_rg->address_to=$alv->address;
                $table_rg->num=$b_t;
                $table_rg->money=$b_t*$tcv->rg_rate;
                $table_rg->save();
                AddressModel::where('id',$alv->id)->update(["$balance_field"=>$b_m,"$guiji_field"=>1]);
                //增加余额
                $res = $this->afterDoRg($tcv->rg_cid,$alv->user_id,$b_t*$tcv->rg_rate);
                Db::commit();
            }
        }
    }

    /**
     * 充币到账操作
     * @param $hjd_cid 系统币种id
     * @param $user_id 充币用户id
     * @param $money 充值金额
     */
    public function afterDoRg($hjd_cid,$user_id,$money){
        return false;
    }

}
