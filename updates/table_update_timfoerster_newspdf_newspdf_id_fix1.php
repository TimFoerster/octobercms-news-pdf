<?php namespace TimFoerster\NewsPdf\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class TableUpdateTimfoersterNewspdfNewspdfIdFix1 extends Migration
{
    public function up()
    {
        Schema::table('timfoerster_newspdf_newspdf', function($table)
        {
            $table->dropColumn('id');
        });
    }
    
    public function down()
    {
        Schema::table('timfoerster_newspdf_newspdf', function($table)
        {
            $table->integer('id')->first();
        });
    }
}
