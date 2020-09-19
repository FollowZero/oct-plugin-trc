<?php namespace Plus\Trc\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePlusTrcRg extends Migration
{
    public function up()
    {
        Schema::create('plus_trc_rg', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('user_id');
            $table->integer('trc_cid');//trc币种
            $table->integer('hjd_cid');//系统币种
            $table->string('address_from', 255);
            $table->string('address_to', 255);
            $table->decimal('num', 14, 4);//trc代币数量
            $table->decimal('money', 14, 4)->nullable();//系统币种金额
            $table->string('txid', 255)->nullable();//txid
            $table->string('remark')->nullable();//备注
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('plus_trc_rg');
    }
}
