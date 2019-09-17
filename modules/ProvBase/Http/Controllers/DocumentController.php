<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\ProvBase;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvBase\Http\Controllers\DocumentTemplateController;

class DocumentController extends \BaseController
{
    /**
     * Create a document; typically called from GUI.
     *
     * @author Patrick Reichel
     */
    public function create()
    {
        $document = null;
        $documenttype_id = $_GET['documenttype_id'] ?? null;
        if (is_null($documenttype_id)) {
            throw new \Exception('No documenttype given');
        }

        $contract_id = $_GET['contract_id'] ?? null;
        if ($contract_id) {
           $model = Contract::find($contract_id);
        }

        $this->create_now($documenttype_id, $model);
    }

    /**
     * Create a document for given documenttype and model.
     * Use this method instead of create() to pass a modified (= extended data).
     *
     * @param   int     The type of document to be created
     * @param   object  The model to use
     *
     * @author Patrick Reichel
     */
    public function create_now($documenttype_id, $model)
    {
        $models = [];
        $model_name = $model->get_model_name();
        if ('Contract' == $model_name) {
            $documenttemplate = $model->get_documenttemplate_by_type($documenttype_id);
        }
        elseif ('PhonenumberManagement' == $model_name) {
            $documenttemplate = $model->Phonenumber->Mta->Modem->Contract->get_documenttemplate_by_type($documenttype_id);
        }
        $controller = new DocumentTemplateController();
        $pdf_file = $controller->render($documenttemplate, $model);

        d(
            $pdf_file,
            'TODO: process created file'
        );

        // @TODO: Process created document:
        //  * move from /tmp* dir, rename file
        //  * add database entry

    }
}
