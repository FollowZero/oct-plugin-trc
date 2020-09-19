<?php namespace Plus\Trc\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePlusTrcWd extends Migration
{
    public function up()
    {
        Schema::create('plus_trc_wd', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('user_id');
            $table->integer('trc_cid');//trc币种
            $table->integer('hjd_cid')->nullable()->default(0);//系统币种
            $table->string('address_from', 255);
            $table->string('address_to', 255);
            $table->decimal('num', 14, 4)->nullable()->default(0.0000);//trcs数量
            $table->decimal('money', 14, 4)->nullable()->default(0.0000);//提币系统金额
            $table->decimal('fee', 12, 2)->nullable()->default(0.00);
            //手续费
            $table->decimal('num_true', 14, 4)->nullable()->default(0.0000);
            //实际到账数量
            $table->string('txid', 255)->nullable();
            $table->string('remark', 255)->nullable();
            $table->integer('status')->nullable()->default(0);//0待审核1已通过-1已拒绝
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('plus_trc_wd');
    }
}
