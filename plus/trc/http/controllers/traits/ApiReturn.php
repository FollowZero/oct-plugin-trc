<?php
namespace Plus\Trc\Http\Controllers\Traits;



trait ApiReturn
{
    protected function apiSuccess($msg=null,$data=null,$status=null)
    {
        $return=[];
        $return['status']=$status??1;
        $return['msg']=$msg??'操作成功';
        $return['data']=$data??[];
        return $return;
    }

    protected function apiError($msg=null,$data=null,$status=null)
    {
        $return=[];
        $return['status']=$status??-1;
        $return['msg']=$msg??'操作失败';
        $return['data']=$data??[];
        return $return;
    }
}