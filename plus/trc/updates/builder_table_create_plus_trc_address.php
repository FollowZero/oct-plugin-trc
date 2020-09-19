<?php namespace Plus\Trc\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePlusTrcAddress extends Migration
{
    public function up()
    {
        Schema::create('plus_trc_address', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('user_id');
            $table->string('address', 255);//地址
            $table->string('address_hex', 255)->nullable();//HEX格式地址
            $table->text('privatekey');//私钥
            $table->decimal('balance_usdt', 14, 4)->nullable();//待归集的usdt数量
            $table->integer('guiji_usdt')->nullable()->default(0);//1需要归集
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('plus_trc_address');
    }
}
