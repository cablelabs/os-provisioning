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

    /**
     * Shows the html links of the related objects recursivly
     * TODO: should be placed in a global concept and not on module base
     */
    $s = '';

    $model = $modem ?? $netgw;
    $parent = $model;
    $classname = explode('\\',get_class($parent));
    $classname = end($classname);

    while ($parent)
    {
        $tmp   = explode('\\',get_class($parent));
        $view  = end($tmp);
        $icon  = $parent->view_icon();
        $label = is_array($ret = $parent->view_index_label()) ? $ret['header'] : $ret;

        $s = "<li>".HTML::decode(HTML::linkRoute($view.'.edit', $icon.$label, $parent->id)).'</li>'.$s;

        $parent = $parent->view_belongs_to();

        if ($parent instanceof \Illuminate\Support\Collection) {
            $parent = $parent->first();
        }
    }

    // Show link to actual site. This depends on if we are in Modem Analysis or CPE Analysis context
    if (! isset($type))
    {
        $route = 'ProvMon';

        if (! Module::collections()->has('ProvMon') && $classname == 'Modem') {
            $route = 'Modem';
        }

        $route .= '.'.($classname == 'Modem' ? 'analysis' : 'netgw');

        $s .= "<li class='nav-tabs'>".HTML::linkRoute($route, trans('view.analysis'), $model->id).'</li>';
    } elseif ($type == 'CPE') {
        $s .= "<li class='nav-tabs'>".HTML::linkRoute('Modem.cpeAnalysis', 'CPE-'.trans('view.analysis'), $model->id).'</li>';
    } elseif ($type == 'MTA') {
        $s .= "<li class='nav-tabs'>".HTML::linkRoute('Modem.mtaAnalysis', 'MTA-'.trans('view.analysis'), $model->id).'</li>';
    }

    echo "<li class='active'><a href='".route("$classname.index")."'><i class='fa fa-hdd-o'></i>$classname</a></li>".$s;
?>
