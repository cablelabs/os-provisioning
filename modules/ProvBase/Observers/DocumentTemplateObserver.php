<?php

namespace Modules\ProvBase\Observers;

class DocumentTemplateObserver
{
    /**
     * Try to get template format from filename suffix
     *
     * @author Patrick Reichel
     */
    protected function get_format($filename)
    {
        $formats = [
            'LaTeX' => ['tex', 'latex'],
        ];
        $suffix = explode('.', $filename);
        $suffix = array_pop($suffix);
        $suffix = strtolower($suffix);
        foreach ($formats as $format => $suffixes)  {
            if (in_array($suffix, $suffixes)) {
                return $format;
            }
        }

        return 'n/a';
    }

    /**
     * @author Patrick Reichel
     */
    public function creating($documenttemplate)
    {
        $documenttemplate->filename_pattern = $documenttemplate->filename_pattern ?: $documenttemplate->documenttype->get_translated_default_filename_pattern();
        $documenttemplate->format = $this->get_format($documenttemplate->file);
        if ($documenttemplate->sepaaccount_id) {
            $documenttemplate->company_id = $documenttemplate->sepaaccount->company->id;
        }
    }

    /**
     * @author Patrick Reichel
     */
    public function updating($documenttemplate)
    {
        $documenttemplate->filename_pattern = $documenttemplate->filename_pattern ?: $documenttemplate->documenttype->get_translated_default_filename_pattern();
        $documenttemplate->format = $this->get_format($documenttemplate->file);
        if ($documenttemplate->sepaaccount_id) {
            $documenttemplate->company_id = $documenttemplate->sepaaccount->company->id;
        }
    }

    /**
     * @author Patrick Reichel
     */
    public function deleting($documenttemplate)
    {
        // check if a base template shall be deleted (which is prohibited)
        if ((0 == $documenttemplate->company_id) && (0 == $documenttemplate->sepaaccount_id)) {
            \Session::push('tmp_error_above_index_list', trans('provbase::messages.documentTemplate.cannotDeleteBaseTemplate'));
            return false;
        }
    }
}
