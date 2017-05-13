<?php namespace TimFoerster\NewsPdf\Controllers;

use Backend\Classes\Controller;
use Illuminate\Http\Response;
use TimFoerster\NewsPdf\Models\NewsPdf as NewsPdfM;
use BackendMenu;

class NewsPdf extends Controller
{
    public $implement = [];
    
    public function __construct()
    {
        parent::__construct();

        // Extending Indikator.News
        BackendMenu::setContext('Indikator.News', 'news', 'posts');
    }

    /**
     * Streams the PDF from the given news_id
     * @param $news_id int id
     * @return mixed stream with generated PDF or error response
     */
    public function pdfPreview($news_id) {

        $model = NewsPdfM::byNews($news_id)->first();

        if($model === null) {
            return Response::create(trans('timfoerster.newspdf::lang.error.no_template_set'), Response::HTTP_NOT_FOUND);
        };

        return $model->generatePdf()->stream('preview.pdf');
    }
}