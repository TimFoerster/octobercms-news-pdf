# PDF support for News and Newsletter

Integrates [DynamicPDF](https://github.com/mplodowski/DynamicPDF) into [Newsletter and News](https://github.com/gergo85/oc-news) to attach/generate PDF's to a post.

## Main feature

* Generate PDF's to a news

## Installation

1. Go to the Settings > Updates & Plugins page in the Backend.
1. Click on the Install plugins button.
1. Type the PDF support for News & Newsletter text in the search field.

## Usage

### In News

* Button for previewing PDF when editing a news. (Note: Save before generating the PDF)
* After every change a new PDF is generated, located in `media/newsletter/:slug.pdf`
 
### Code

`Indikator\News\Models\Posts` has two new relations:
* pdf: Is the attached pdf file (see `System\Models\File`)
* newsPdf:  `TimFoerster\NewsPdf\Models\NewsPdf` the extended model with a `template_code` field, where the current selected template is stored. 
 
### PDF Template
 
To change a PDF layout or template, see [DynamicPdf](https://github.com/mplodowski/DynamicPDF).
