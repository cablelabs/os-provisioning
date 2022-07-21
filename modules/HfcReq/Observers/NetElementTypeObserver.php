<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Modules\HfcReq\Observers;

use Modules\HfcReq\Entities\NetElementType;

class NetElementTypeObserver
{
    public function created($netElementType)
    {
        NetElementType::where('id', $netElementType->id)
            ->update(['base_type_id' => $this->getBaseTypeId($netElementType->id)]);
    }

    public function updating($netElementType)
    {
        if (! $netElementType->isDirty('parent_id')) {
            return;
        }

        $baseTypeId = $this->getBaseTypeId($netElementType->id);
        $netElementType->base_type_id = $baseTypeId;
        $netElementType->netelements()->update(['base_type_id' => $baseTypeId]);
    }

    public function deleting($netElementType)
    {
        NetElementType::whereIn('id', $netElementType->children->pluck('id'))->update([
            'parent_id' => $netElementType->parent_id,
        ]);
    }

    public function restoring($netElementType)
    {
        if (! NetElementType::find($netElementType->parent_id)) {
            $netElementType->parent_id = null;
        }
    }

    /**
     * Return the base type id of the current NetElementType
     *
     * @note: base device means: parent_id = 0, 2 (cluster)
     *
     * @param  int  $netElementTypeId
     * @return int id of base device netelementtype
     */
    public function getBaseTypeId(int $netElementTypeId): int
    {
        $ancestors = NetElementType::whereAncestorOrSelf($netElementTypeId)
            ->withDepth()
            ->get();
        $special = $ancestors->whereIn('id', [2, 9]);

        return $ancestors
            ->when($special->count(),
                fn () => $special->first(),
                fn ($ancestors) => $ancestors->firstWhere('depth', 0)
            )->id;
    }
}
