<?php

namespace Plus\Trc\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Input;
use Plus\Trc\Classes\TRC20;
use Plus\Trc\Http\Controllers\Traits\ApiReturn;
use Plus\Trc\Models\AddressModel;
use Plus\Trc\Models\CurrencyModel;
use Plus\Trc\Models\WdModel;
use RLuders\JWTAuth\Classes\JWTAuth;
use Validator;
use Db;
class WithController extends Controller
{
    use ApiReturn;
    /**
     * 提币规则
     */
    public function rule(JWTAuth $auth){
        if (!$user = $auth->user()) {
            return $this->apiError('登录失效,请重新登录');
        }
        //TRC代币信息,获取节点
        $trc_currencies=CurrencyModel::select('id','title','wd_cid','wd_min','wd_max','wd_mul','wd_reta','wd_fee')->where('status',1)->where('wd_status',1)->get();
        foreach ($trc_currencies as &$tcv){
            //系统币信息

            //余额
            $tcv['had_money']=0.00;
        }
        $rdata['currencies']=$trc_currencies;

        return $this->apiSuccess('操作成功',$rdata);
    }

    /**
     * 提币操作
     */
    public function doWd(JWTAuth $auth){
        if (!$user = $auth->user()) {
            return $this->apiError('登录失效,请重新登录');
        }
        $trc_cid=Input::get('trc_cid');
        $trc_currency=CurrencyModel::where('status',1)->where('wd_status',1)->where('id',$trc_cid)->first();
        if(!$trc_currency){
            return $this->apiError('参数错误');
        }
        if($trc_currency->wd_max>0){
            $num_rule="required|numeric|min:$trc_currency->wd_min|max:$trc_currency->wd_max";
        }else{
            $num_rule="required|numeric|min:$trc_currency->wd_min";
        }
        $rules=[
            'address'=>'required',
            'num'=>$num_rule,
        ];
        $messages=[
            'address.required'=>'提币地址不能为空',
            'num.required'=>'请输入提币数量',
            'num.numeric'=>'提币数量必须是数字',
            'num.min'=>'提币数量最小是:'.$trc_currency->wd_min,
            'num.max'=>'提币数量最大是:'.$trc_currency->wd_max,
        ];
        $validator = Validator::make(Input::all(), $rules,$messages);
        if ($validator->fails()) {
            return $this->apiError($validator);
        }
        $address=Input::get('address');
        $num=Input::get('num');
        //整数倍验证
        $wd_mul=$trc_currency->wd_mul;
        if($wd_mul>0){
            if(($num%$wd_mul)!=0){
                return $this->apiError($wd_mul.'整数倍提币');
            }
        }
        //验证地址是否波场地址
        $node=$trc_currency->node->api??'';
        $new_address=$trc_currency->main??'';
        $new_key=$trc_currency->main_pwd??'';
        if(!$node){
            return $this->apiError('配置错误');
        }
        $tron=new TRC20($node,$new_address,$new_key);
        $is_add=$tron->validateAddress($address);
        if((!$is_add['result'])||($is_add['message']!='Base58check format')){
            return $this->apiError('地址格式错误');
        }
        //判断地址是否系统生成的地址。可内部转账或拒绝转账
        $add_info=AddressModel::where('address',$address)->first();
        if($add_info){
            return $this->apiError('地址格式错误2');
        }
        //支付密码验证
        //判断余额
        $money=$num*$trc_currency->wd_rate;
        $wd_fee=$trc_currency->wd_fee;
        Db::beginTransaction();
        try{
            //扣除系统币
            //增加提币记录
            $table_wd=new WdModel();
            $table_wd->user_id=$user->id;
            $table_wd->trc_cid=$trc_currency->id;
            $table_wd->hjd_cid=$trc_currency->wd_cid;
            $table_wd->address_from=$new_address;
            $table_wd->address_to=$address;
            $table_wd->num=$num;
            $table_wd->money=$money;
            $table_wd->fee=$wd_fee;
            $table_wd->num_true=$num-$wd_fee;
            $table_wd->save();
            Db::commit();
            return $this->apiSuccess('操作成功');
        }catch (\Exception $e){
            Db::rollBack();
            return $this->apiError($e->getMessage());
        }
    }


}
