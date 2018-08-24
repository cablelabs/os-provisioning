<?php

namespace App;

trait AddressFunctionsTrait
{
    /**
     * Helper to define possible salutation values.
     * E.g. envia TEL API has a well defined set of valid values – using this method we can handle this.
     *
     * @author Patrick Reichel
     */
    public function get_salutation_options()
    {
        $defaults = [
            '',
            'Herr',
            'Frau',
            'Firma',
            'Behörde',
        ];

        if (\Module::collections()->has('ProvVoipEnvia')) {

            // envia TEL expects Herrn instead of Herr ⇒ to be as compatible as possible to other use cases
            // we nevertheless store Herr in database and fix this in XML generation within
            // ProvVoipEnvia->_add_fields
            $options = [
                '',
                'Herr',
                'Frau',
                'Firma',
                'Behörde',
            ];
        } else {
            $options = $defaults;
        }

        $result = [];
        foreach ($options as $option) {
            $result[$option] = $option;
        }

        return $result;
    }

    /**
     * Helper to define possible academic degree values.
     * E.g. envia TEL API has a well defined set of valid values – using this method we can handle this.
     *
     * @author Patrick Reichel
     */
    public function get_academic_degree_options()
    {
        $defaults = [
            '',
            'Dr.',
            'Prof. Dr.',
        ];

        if (\Module::collections()->has('ProvVoipEnvia')) {
            $options = [
                '',
                'Dr.',
                'Prof. Dr.',
            ];
        } else {
            $options = $defaults;
        }

        $result = [];
        foreach ($options as $option) {
            $result[$option] = $option;
        }

        return $result;
    }
}
