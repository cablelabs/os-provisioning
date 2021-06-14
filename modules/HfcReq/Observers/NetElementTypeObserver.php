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
        $netElementType->update(['base_type' => $this->getBaseType($netElementType)]);
    }

    public function updated($netElementType)
    {
        if (! $netElementType->wasRecentlyCreated &&
            ! $netElementType->isDirty('base_type') &&
            $netElementType->isDirty('parent_id')) {
            $netElementType->update(['base_type' => $this->getBaseType($netElementType)]);
        }
    }

    public function deleting($netElementType)
    {
        // update without Events to save 1 query per NetElementType
        foreach ($netElementType->children as $nET) {
            NetElementType::where('id', $nET->id)->update([
                'parent_id' => $netElementType->parent_id,
            ]);
        }
    }

    /**
     * Return the base type id of the current NetElementType
     *
     * @note: base device means: parent_id = 0, 2 (cluster)
     *
     * @param $netElementType
     * @return int id of base device netelementtype
     */
    public function getBaseType($netElementType)
    {
        $p = $netElementType;

        while (! $p->parent_id && ! in_array($p->id, [2, 9])) {
            $p = $p->parent;
        }

        return $p->id;
    }
}
