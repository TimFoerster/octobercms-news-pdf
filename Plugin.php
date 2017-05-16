<?php namespace TimFoerster\NewsPdf;

use Indikator\News\Models\Posts;
use System\Classes\PluginBase;
use Event;
use TimFoerster\NewsPdf\Models\NewsPdf;
use Storage;
use System\Models\File;
use Backend;

class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'timfoerster.newspdf::lang.plugin.name',
            'description' => 'timfoerster.newspdf::lang.plugin.description',
            'author'      => 'TimFoerster',
            'icon'        => 'icon-file-pdf',
            'homepage'    => 'https://github.com/TimFoerster/octobercms-news-pdf'
        ];
    }

    /**
     * @var array Plugin dependencies
     */
    public $require = ['Indikator.News', 'Renatio.DynamicPDF'];

    public function boot()
    {
        // extend Posts
        Posts::extend(function($model) {
            $model->hasOne['newsPdf'] = ['TimFoerster\NewsPdf\Models\NewsPdf', 'key' => 'post_id'];
            $model->attachOne['pdf'] = ['System\Models\File'];
        });

        //Listen to saved event to generate new pdf
        Posts::saved(function ($news) {

            $model = NewsPdf::byNews($news->id)->first();

            if ($model === null) {
                return ;
            };

            $model->template_code = $news->newsPdf->template_code;
            $path = 'media/newsletter';
            Storage::makeDirectory($path);

            $path = storage_path("app/".$path.'/'.$news->slug.'.pdf');

            $model->generatePdf()->save($path);

            $file = new File;
            $file->fromFile($path);

            $news->pdf()->add($file);
        });

        // Extend all backend form usage
        Event::listen('backend.form.extendFields', function($widget) {

            // Only for the Posts controller
            if (!$widget->getController() instanceof \Indikator\News\Controllers\Posts) {
                return;
            }

            // Only for the Posts model
            if (!$widget->model instanceof \Indikator\News\Models\Posts) {
                return;
            }

            // Attach relation
            if ($widget->context == 'create') {
                $widget->model->newsPdf = new NewsPdf();
            } elseif ($widget->context == 'update') {
                $widget->model->newsPdf = NewsPdf::byNews($widget->model->id)->get();
            }

            // Add extra field
            $widget->addFields([
                'newsPdf[template_code]' => [
                    'label'   => trans('timfoerster.newspdf::lang.fields.template_code.label'),
                    'comment' => trans('timfoerster.newspdf::lang.fields.template_code.comment'),
                    'type'    => 'dropdown',
                    'options' => \Renatio\DynamicPDF\Models\Template::lists('title','code'),
                    'tab'     => trans('timfoerster.newspdf::lang.tab')
                ]
            ], 'primary');

        });

        // Add button
        Event::listen('indikator.news.extendPostUpdateViewButtons', function($controller, $formModel) {
            return '
                <a class="btn btn-info"
                   target="_blank"
                   href="'.Backend::url("timfoerster/newspdf/newspdf/pdfpreview/" . $formModel->id).'">
                   '.trans("renatio.dynamicpdf::lang.templates.preview_pdf").'
                </a>
            ';
        });
    }
}
