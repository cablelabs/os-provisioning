<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'El :attribute debe ser aceptado.',
    'active_url'           => 'El :attribute no es un URL valido.',
    'after'                => 'El :attribute debe ser una fecha despues de :date.',
    'after_or_equal'       => 'El :attribute debe ser una fecha posterior o igual a :date.',
    'alpha'                => 'El :attribute solo puede contener letras.',
    'alpha_dash'           => 'El :attribute solo puede contener letras, numeros, y guiones.',
    'alpha_num'            => 'El :attribute solo puede contener letras y numeros.',
    'array'                => 'El :attribute debe ser un array.',
    'available'            => 'No hay entrada disponible en los a. de config. - Por favor inserte :attribute',
    'before'               => 'El :attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => 'El :attribute debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file'    => 'El :attribute debe estar entre :min y :max kbs.',
        'string'  => 'El :attribute debe tener entre :min y :max caracteres.',
        'array'   => 'El :attribute debe tener entre :min y :max articulos.',
    ],
    'boolean'              => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'            => 'El :attribute de confirmacion no coincide.',
    'date'                 => 'El :attribute no es una fecha valida.',
    'dateornull'           => 'Tiene que ser una fecha valida o vacia',
    'date_format'          => 'El :attribute no coincide con el formato :format.',
    'different'            => 'El :attribute y :other deben ser diferentes.',
    'digits'               => 'El :attribute debe ser de :digits digitos.',
    'digits_between'       => 'El :attribute debe estar entre :min y :max digitos.',
    'dimensions'           => 'El :attribute tiene dimensiones de imagen no válidas.',
    'distinct'             => 'El :attribute tiene un valor duplicado.',
    'email'                => 'El :attribute debe ser un correo electronico valido.',
    'file'                 => 'El :attribute debe ser un archivo.',
    'filled'               => 'El campo :attribute es requirido.',
    'exists'               => 'El :attribute seleccionado no es valido.',
    'image'                => 'El :attribute debe ser una imagen.',
    'in'                   => 'El :attribute seleccionado no es valido.',
    'integer'              => 'El :attribute debe ser un integer.',
    'in_array'             => 'El :attribute campo no existe en :other.',
    'max'                  => [
        'numeric' => 'El :attribute no debe ser mayor que :max.',
        'file'    => 'El :attribute no debe ser mayor que :max kilobytes.',
        'string'  => 'El :attribute no debe ser mayor que :max caracteres.',
        'array'   => 'El :attribute no debe tener mas de :max articulos.',
    ],
    'mimes'                => 'El :attribute debe ser un archivo de tipo :values.',
    'mimetypes'            => 'Mime type erroneo. Pide ayuda!',
    'min'                  => [
        'numeric' => 'El :attribute debe ser por lo menos :min.',
        'file'    => 'El :attribute debe ser por lo menos :min kilobytes.',
        'string'  => 'El :attribute debe ser por lo menos :min caracteres.',
        'array'   => 'El :attribute debe tener por lo menos :min articulos.',
    ],
    'not_in'               => 'El :attribute seleccionado no es valido.',
    'numeric'              => 'El :attribute debe ser a numero.',
    'period'               => ':attribute tiene un formato invalido',
    'present'              => 'El campo :attribute debe estar presente.',
    'product' 			=> '¡Los tipos de productos deben escribirse como en el mensaje de ayuda y separados por comas!',
    'regex'                => 'El formato :attribute no es valido.',
    'required'             => 'El campo :attribute es requerido.',
    'required_if'          => 'El campo :attribute es requerido cuando :other es :value.',
    'required_with'        => 'El campo :attribute es requerido cuando :values esta presente.',
    'required_with_all'    => 'El campo :attribute es requerido cuando :values esta presente.',
    'required_without'     => 'El campo :attribute es requerido cuando :values no esta presente.',
    'required_without_all' => 'El campo :attribute es requerido cuando ninguno de los :values estan presentes.',
    'same'                 => 'El :attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El :attribute debe ser :size.',
        'file'    => 'El :attribute debe ser :size kilobytes.',
        'string'  => 'El :attribute debe ser :size caracteres.',
        'array'   => 'El :attribute debe contener :size articulos.',
    ],
    'timezone'             => 'El :attribute debe ser una zona valida.',
    'unique'               => 'El :attribute ya ha sido tomado.',
    'uploaded'             => 'El :attribute no se pudo cargar.',
    'url'                  => 'El formato :attribute no es valido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'password' => [
            'regex' => 'The password has to be at least 10 characters long and fulfill three of these requirements:
                        <li>Uppercase characters (A – Z)</li>
                        <li>Lowercase characters (a – z)</li>
                        <li>Digits (0 – 9)</li>
                        <li>Non-alphanumeric (For example: !, $, #, or %)</li>
                        <li>Unicode characters</li>',
        ],
    ],

    'docsis'               => ':attribute',
    'ip'                   => 'No es una direccion IP valida, de la forma: 192.168.0.255',
    'ip_in_range'          => 'La direccion IP no esta dentro del rango especificado anteriormente',
    'ip_larger'            => 'La direccion IP debe tener una cifra mayor debido a lo especificado en campos anteriores',
    'mac'                  => 'El :attribute debe ser una direccion MAC de la forma: aa:bb:cc:dd:ee:ff',
    'netmask'              => 'No es una netmask correcta',
    'not_null'             => 'Este campo tiene que ser establecido (no 0)',
    'null_if'              => 'Tiene que ser cero',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => 'nombre',
        'username' => 'usuario',
        'email' => 'correo electrónico',
        'first_name' => 'nombre',
        'last_name' => 'apellido',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de la contraseña',
        'city' => 'ciudad',
        'country' => 'país',
        'address' => 'dirección',
        'phone' => 'teléfono',
        'mobile' => 'móvil',
        'age' => 'edad',
        'sex' => 'sexo',
        'gender' => 'género',
        'day' => 'día',
        'month' => 'mes',
        'year' => 'año',
        'hour' => 'hora',
        'minute' => 'minuto',
        'second' => 'segundo',
        'title' => 'título',
        'content' => 'contenido',
        'description' => 'descripción',
        'excerpt' => 'extracto',
        'date' => 'fecha',
        'time' => 'hora',
        'available' => 'Disponible',
        'size' => 'Tamaño',
    ],
];
