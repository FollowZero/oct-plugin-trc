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

class TestController extends Controller
{
    use ApiReturn;
    /**
     * 测试
     */
    public function index(){
        $address='TWBVCauL5ub9eudQxonJsQ8459h7PhghiK';
        //TRC代币信息,获取节点
        $trc_currency=CurrencyModel::with(['node'])->where('status',1)->first();
        $node=$trc_currency->node->api??'';
        $new_address=$trc_currency->main??'';
        $new_key=$trc_currency->main_pwd??'';
        if(!$node){
            return $this->apiError('配置错误');
        }
        //测试生成地址
        $tron=new TRC20($node,$new_address,$new_key);
        //激活地址
        $b_d=$tron->createAccount();

        return $this->apiSuccess('操作成功',$b_d);
    }


}
