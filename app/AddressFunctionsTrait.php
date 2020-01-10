<?php

namespace App;

use Storage;

trait AddressFunctionsTrait
{
    private $formoptions_path = 'config/provbase/formoptions/';

    /**
     * Helper to define possible salutation values.
     * E.g. envia TEL API has a well defined set of valid values – using this method we can handle this.
     *
     * @author Patrick Reichel
     */
    public function getSalutationOptions($type = 'all')
    {
        $person_file = $this->formoptions_path.'salutations_person.txt';
        $institution_file = $this->formoptions_path.'salutations_institution.txt';

        if (\Module::collections()->has('ProvVoipEnvia')) {
            // special handling needed for Envia TEL – only certain values are allowed

            // envia TEL expects Herrn instead of Herr ⇒ to be as compatible as possible to other use cases
            // we nevertheless store Herr in database and fix this in XML generation within
            // ProvVoipEnvia->_add_fields
            $persons = [
                'Herr',
                'Frau',
            ];
            $institutions = [
                'Firma',
                'Behörde',
            ];
        } else {
            $persons = [];
            // do not “explode” at “\n” here – there is a real danger of files edited in Windows environments
            $tmp = preg_split('/\r\n|\r|\n/', Storage::get($person_file));
            foreach ($tmp as $person) {
                $person = trim($person);
                if ($person) {
                    $persons[] = $person;
                }
            }
            $institutions = [];
            // do not “explode” at “\n” here – there is a real danger of files edited in Windows environments
            $tmp = preg_split('/\r\n|\r|\n/', Storage::get($institution_file));
            foreach ($tmp as $institution) {
                $institution = trim($institution);
                if ($institution) {
                    $institutions[] = $institution;
                }
            }
        }

        $result = [];

        if ('person' == $type) {
            foreach ($persons as $person) {
                $result[$person] = $person;
            }
        } elseif ('institution' == $type) {
            foreach ($institutions as $institution) {
                $result[$institution] = $institution;
            }
        } else {
            $result[''] = ''; // add empty option
            foreach ($persons as $person) {
                $result[$person] = $person;
            }
            foreach ($institutions as $institution) {
                $result[$institution] = $institution;
            }
        }

        return $result;
    }

    /**
     * Wrapper to get person salutation options.
     *
     * @author Patrick Reichel
     */
    public function getSalutationOptionsPerson()
    {
        return $this->getSalutationOptions('person');
    }

    /**
     * Wrapper to get institution salutation options.
     *
     * @author Patrick Reichel
     */
    public function getSalutationOptionsInstitution()
    {
        return $this->getSalutationOptions('institution');
    }

    /**
     * Helper to define possible academic degree values.
     * E.g. envia TEL API has a well defined set of valid values – using this method we can handle this.
     *
     * @author Patrick Reichel
     */
    public function getAcademicDegreeOptions()
    {
        $degree_file = $this->formoptions_path.'academic_degrees.txt';

        if (\Module::collections()->has('ProvVoipEnvia')) {
            // special handling needed for Envia TEL – only certain values are allowed
            $degrees = [
                'Dr.',
                'Prof. Dr.',
            ];
        } else {
            $degrees = [];
            // do not “explode” at “\n” here – there is a real danger of files edited in Windows environments
            $tmp = preg_split('/\r\n|\r|\n/', Storage::get($degree_file));
            foreach ($tmp as $degree) {
                $degree = trim($degree);
                if ($degree) {
                    $degrees[] = $degree;
                }
            }
        }

        $result = [];
        $result[''] = ''; // add empty option
        foreach ($degrees as $degree) {
            $result[$degree] = $degree;
        }

        return $result;
    }
}
