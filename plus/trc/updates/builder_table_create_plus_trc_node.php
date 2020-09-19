<?php namespace Plus\Trc\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePlusTrcNode extends Migration
{
    public function up()
    {
        Schema::create('plus_trc_node', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('name');
            $table->string('title');
            $table->string('api')->nullable();//api接口地址
            $table->string('browser')->nullable();//区块浏览器查询地址
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('plus_trc_node');
    }
}
