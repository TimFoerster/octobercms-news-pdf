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
            $model->hasOne['newsPdf'] = ['TimFoerster\NewsPdf\Models\NewsPdf', 'key' => 'post_id', 'otherKey' => 'id', 'default' => true];
            $model->attachOne['pdf'] = ['System\Models\File'];

            $model->bindEvent('model.afterCreate', function() use ($model) {
                $model->newsPdf()->post_id = $model->id;
            });
        });

        // listen to duplicate event
        Event::listen('indikator.news.posts.duplicate', function(&$clone, $post) {
            $newsPdf = new NewsPdf();
            $newsPdf->post_id = $clone->id;
            $newsPdf->template_code = $post->template_code;
            $newsPdf->save();
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

                // To prevent errors, attach new model if it's not found
                if($widget->model->newsPdf === null) {
                    $widget->model->newsPdf = new NewsPdf();
                }
            }

            // Replace introductory and content field with a custom toolbar.
            $widget->removeField('introductory');
            $widget->removeField('content');
            $widget->removeField('enable_newsletter_content');
            $widget->removeField('newsletter_content');


            $toolbarButtons = "fullscreen|bold|italic|underline|strikeThrough|subscript|superscript|fontFamily|fontSize|||color|emoticons|inlineStyle|paragraphStyle|||paragraphFormat|align|formatOL|formatUL|outdent|indent|quote|insertHR|-|insertLink|insertImage|insertTable|undo|redo|clearFormatting|selectAll|html";

            $widget-> addTabFields([
                'introductory' => [
                    'tab'   => 'indikator.news::lang.form.introductory',
                    'type'    => 'richeditor',
                    'toolbarButtons' => $toolbarButtons,
                    'size' => 'large',
                ],
                'content' => [
                    'tab'   => 'indikator.news::lang.form.content',
                    'type'    => 'richeditor',
                    'toolbarButtons' => $toolbarButtons,
                    'size' => 'giant',
                ],
                'enable_newsletter_content' => [
                    'tab'   => 'indikator.news::lang.form.newsletter_content_tab',
                    'label' => 'indikator.news::lang.form.enable_newsletter_content',
                    'comment' => 'indikator.news::lang.form.enable_newsletter_content_description',
                    'type'    => 'switch',
                    'default' => false,
                    'context' => 'update',
                ],
                'newsletter_content' => [
                    'tab'   => 'indikator.news::lang.form.newsletter_content_tab',
                    'label' => 'indikator.news::lang.form.newsletter_content',
                    'type'    => 'richeditor',
                    'toolbarButtons' => $toolbarButtons,
                    'size' => 'giant',
                    'context' => 'update'
                ]
            ]);

            // Add template_code field
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
