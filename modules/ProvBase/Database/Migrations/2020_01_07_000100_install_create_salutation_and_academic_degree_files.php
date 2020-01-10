<?php

class InstallCreateSalutationAndAcademicDegreeFiles extends BaseMigration
{
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
