    <?php
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

            $route .= '.'.($classname == 'Modem' ? 'index' : 'netgw');

            $s .= "<li class='nav-tabs'>".HTML::linkRoute($route, trans('view.analysis'), $model->id).'</li>';
        } elseif ($type == 'CPE') {
            $s .= "<li class='nav-tabs'>".HTML::linkRoute('Modem.cpeAnalysis', 'CPE-'.trans('view.analysis'), $model->id).'</li>';
        } elseif ($type == 'MTA') {
            $s .= "<li class='nav-tabs'>".HTML::linkRoute('Modem.mtaAnalysis', 'MTA-'.trans('view.analysis'), $model->id).'</li>';
        }

        echo "<li class='active'><a href='".route("$classname.index")."'><i class='fa fa-hdd-o'></i>$classname</a></li>".$s;
    ?>
