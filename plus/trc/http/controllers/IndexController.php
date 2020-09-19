<?php

namespace Plus\Trc\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Input;
use Plus\Trc\Classes\TRC20;
use Plus\Trc\Http\Controllers\Traits\ApiReturn;
use Plus\Trc\Models\AddressModel;
use Plus\Trc\Models\CurrencyModel;
use RLuders\JWTAuth\Classes\JWTAuth;

class IndexController extends Controller
{
    use ApiReturn;
    /**
     * 测试
     */
    public function address(JWTAuth $auth){
        if (!$user = $auth->user()) {
            return $this->apiError('登录失效,请重新登录');
        }
        $address='';
        //会员地址信息
        $user_address=AddressModel::where('user_id',$user->id)->first();
        if($user_address){
            $address=$user_address->address;
        }else{
            //TRC代币信息,获取节点
            $trc_currency=CurrencyModel::with(['node'])->where('status',1)->first();
            $node=$trc_currency->node->api??'';
            $new_address=$trc_currency->main??'';
            $new_key=$trc_currency->main_pwd??'';
            if(!$node){
                return $this->apiError('配置错误');
            }
            //生成地址
            $tron=new TRC20($node,$new_address,$new_key);
            $res_add=$tron->createAccount();
            //加密存库
            $table_add=new AddressModel();
            $table_add->user_id=$user->id;
            $table_add->address=$res_add['address'];
            $table_add->address_hex=$res_add['hexAddress'];
            $table_add->privatekey=$res_add['privateKey'];
            $table_add->save();
            $address=$res_add['address'];
            //激活地址
            $res_reg=$tron->registerAccount($trc_currency->main,$address);
        }
        return $this->apiSuccess('操作成功',$address);
    }


}
