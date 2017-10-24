<?php namespace TimFoerster\NewsPdf\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class TableUpdateTimfoersterNewspdfNewspdfIdFix2 extends Migration
{
    public function up()
    {
        Schema::table('timfoerster_newspdf_newspdf', function($table)
        {
            $table->increments('id')->first();
        });
    }
    
    public function down()
    {
        Schema::table('timfoerster_newspdf_newspdf', function($table)
        {
            $table->dropColumn('id');
        });
    }
}
