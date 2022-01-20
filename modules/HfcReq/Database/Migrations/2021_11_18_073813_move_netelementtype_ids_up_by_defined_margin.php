<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;
use Modules\HfcReq\Entities\NetElement;
use Modules\HfcReq\Entities\NetElementType;

class MoveNetelementtypeIdsUpByDefinedMargin extends BaseMigration
{
    public $migrationScope = 'database';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $modelsToUpdate = NetElementType::withTrashed()->whereNotIn('id', array_keys(NetElementType::$undeletables))->where('id', '<', 1000)->get();

        if (! $modelsToUpdate->count() && DB::select("SHOW TABLE STATUS LIKE 'netelementtype'")[0]->Auto_increment >= 1000) {
            Log::info('All NetElementType IDs are okay. The offset was correctly set.');

            return;
        }

        // should never run, but will leave a notice in the Log, as well as at the console if it is executed
        Log::warning('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        Log::warning('The offset for NetElementType IDs is not correctly set. Trying to fix it!');
        Log::warning('Please check the Database if everything was migrated correctly!');
        Log::warning('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');

        NetElementType::unguard();
        $id = NetElementType::withTrashed()->where('id', '>', 1000)->count() ? NetElementType::withTrashed()->pluck('id')->max() + 1 : 1000;

        dump('Updating NetElementType IDs');
        foreach ($modelsToUpdate as $netelementtype) {
            dump("Processing ID {$netelementtype->id}, moving to {$id}");
            NetElement::withTrashed()
                ->where('netelementtype_id', $netelementtype->id)
                ->update(['netelementtype_id' => $id]);

            NetElementType::withTrashed()
                ->where('parent_id', $netelementtype->id)
                ->update(['parent_id' => $id]);

            NetElementType::withTrashed()
                ->where('base_type', $netelementtype->id)
                ->update(['base_type' => $id]);

            if (Module::collections()->has('HfcSnmp')) {
                \Modules\HfcSnmp\Entities\Parameter::withTrashed()
                    ->where('netelementtype_id', $netelementtype->id)
                    ->update(['netelementtype_id' => $id]);
            }

            $netelementtype->update(['id' => $id]);
            $id++;
        }

        dump("Setting auto increment for Netelementtype to {$id}");
        DB::statement("ALTER TABLE netelementtype AUTO_INCREMENT = {$id};");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
