<?php namespace TimFoerster\NewsPdf\Models;

use Model;
use Renatio\DynamicPDF\Classes\PDF;
use Renatio\DynamicPDF\Classes\PDFWrapper;

/**
 * Model
 */
class NewsPdf extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'timfoerster_newspdf_newspdf';

    public $belongsTo = [
        'news' => ['Indikator\News\Models\Posts', 'key' => 'post_id']
    ];

    public function scopeByNews($query, $newsId) {
        return $query->where('post_id','=',$newsId);
    }

    /**
     * Generates the pdf with the stored template_code of this model
     * @return PDFWrapper
     */
    public function generatePdf()
    {
        $templateCode = $this->template_code;
        $content =  $this->news->content;

        //replace src
        $url = url('/');
        $content = preg_replace( '/src="\/([^"]*)"/i', 'src="'.$url.'/$1"', $content ) ;

        $data = ['title' => $this->news->title, 'content' => $content];

        return PDF::loadTemplate($templateCode, $data);
    }


}