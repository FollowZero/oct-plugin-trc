<?php namespace Plus\Trc\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePlusTrcCurrency extends Migration
{
    public function up()
    {
        Schema::create('plus_trc_currency', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('name');
            $table->string('title');
            $table->string('symbol')->nullable();
            $table->integer('status')->nullable()->default(1);
            $table->string('contract')->nullable();//合约地址
            $table->integer('decimals')->nullable()->default(6);//精度
            $table->string('main')->nullable();//主地址
            $table->string('main_hex')->nullable();//hex格式地址
            $table->text('main_pwd')->nullable();//私钥
            $table->integer('node_id')->nullable();//节点网络
            $table->integer('rg_cid')->nullable();//充币币种
            $table->integer('rg_status')->nullable()->default(1);//充币开关
            $table->decimal('rg_rate', 12, 2)->nullable()->default(0.00);//充币汇率
            $table->integer('wd_cid')->nullable();//提币币种
            $table->integer('wd_status')->nullable()->default(1);//提币开关
            $table->integer('wd_min')->nullable()->default(0);//最小提币数量
            $table->integer('wd_max')->nullable()->default(0);//最大提币数量
            $table->integer('wd_mul')->nullable()->default(1);//提币倍数
            $table->decimal('wd_rate', 12, 2)->nullable()->default(0.00);//提币手汇率
            $table->decimal('wd_fee', 12, 2)->nullable()->default(0.00);//提币手续费
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('plus_trc_currency');
    }
}
