<?php namespace TimFoerster\NewsPdf\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimfoersterNewspdfNewspdf extends Migration
{
    public function up()
    {
        Schema::table('timfoerster_newspdf_newspdf', function($table)
        {
            $table->integer('post_id')->nullable()->unsigned();
        });
    }
    
    public function down()
    {
        Schema::table('timfoerster_newspdf_newspdf', function($table)
        {
            $table->dropColumn('post_id');
        });
    }
}
