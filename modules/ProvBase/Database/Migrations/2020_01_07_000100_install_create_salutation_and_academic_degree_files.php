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
class InstallCreateSalutationAndAcademicDegreeFiles extends BaseMigration
{
    public $migrationScope = 'system';

    protected $path = 'config/provbase/formoptions/';
    protected $salutations_person_file = 'salutations_person.txt';
    protected $salutations_institution_file = 'salutations_institution.txt';
    protected $academic_degrees_file = 'academic_degrees.txt';

    public function up()
    {
        $default_salutations_person = implode("\n", [
            'Frau',
            'Herr',
        ]);
        $default_salutations_institution = implode("\n", [
            'Firma',
            'Behörde',
        ]);
        $default_academic_degrees = implode("\n", [
            'Dr.',
            'Prof. Dr.',
        ]);

        \Storage::makeDirectory($this->path);

        \Storage::put($this->path.$this->salutations_person_file, $default_salutations_person);
        \Storage::put($this->path.$this->salutations_institution_file, $default_salutations_institution);
        \Storage::put($this->path.$this->academic_degrees_file, $default_academic_degrees);
    }

    public function down()
    {
        \Storage::delete($this->path.$this->salutations_person_file);
        \Storage::delete($this->path.$this->salutations_institution_file);
        \Storage::delete($this->path.$this->academic_degrees_file);
        \Storage::deleteDirectory($this->path);
    }
}
