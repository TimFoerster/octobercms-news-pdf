<?php namespace TimFoerster\NewsPdf\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateTimfoersterNewspdfNewspdf extends Migration
{
    public function up()
    {
        Schema::create('timfoerster_newspdf_newspdf', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->string('template_code')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('timfoerster_newspdf_newspdf');
    }
}
