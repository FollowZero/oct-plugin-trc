<?php
/**
 * TRC20接口
 */
Route::group(
    [
        'prefix' => 'api/trc/',
        'namespace' => 'Plus\Trc\Http\Controllers',
        'middleware'=>['api'],
    ],
    function () {
        //测试
        Route::post(
            'test',
            'TestController@index'
        )->name('api.trc.test');

        Route::middleware(['jwt.auth'])->group(
            function () {
                //充币地址
                Route::post(
                    'address',
                    'IndexController@address'
                )->name('api.trc.address');
                //提币规则
                Route::post(
                    'withRule',
                    'WithController@rule'
                )->name('api.trc.withRule');
                //提币操作
                Route::post(
                    'doWd',
                    'WithController@doWd'
                )->name('api.trc.doWd');
            }
        );
    }
);



