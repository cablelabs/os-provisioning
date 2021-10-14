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

    'accepted' => 'El :attribute debe ser aceptado.',
    'active_url' => 'El :attribute no es un URL valido.',
    'after' => 'El :attribute debe ser una fecha despues de :date.',
    'after_or_equal' => 'El :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El :attribute solo puede contener letras.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'El :attribute solo puede contener letras y numeros.',
    'array' => 'El :attribute debe ser un array.',
    'before' => 'El :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file' => 'El :attribute debe estar entre :min y :max kbs.',
        'string' => 'El :attribute debe tener entre :min y :max caracteres.',
        'array' => 'El :attribute debe tener entre :min y :max articulos.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'El :attribute de confirmacion no coincide.',
    'date' => 'El :attribute no es una fecha valida.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'El :attribute no coincide con el formato :format.',
    'different' => 'El :attribute y :other deben ser diferentes.',
    'digits' => 'El :attribute debe ser de :digits digitos.',
    'digits_between' => 'El :attribute debe estar entre :min y :max digitos.',
    'dimensions' => 'El :attribute tiene dimensiones de imagen no válidas.',
    'distinct' => 'El :attribute tiene un valor duplicado.',
    'email' => 'El :attribute debe ser un correo electronico valido.',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'exists' => 'El :attribute seleccionado no es valido.',
    'file' => 'El :attribute debe ser un archivo.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'El :attribute debe ser una imagen.',
    'in' => 'El :attribute seleccionado no es valido.',
    'in_array' => 'El :attribute campo no existe en :other.',
    'integer' => 'El :attribute debe ser un integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'El :attribute no debe ser mayor que :max.',
        'file' => 'El :attribute no debe ser mayor que :max kilobytes.',
        'string' => 'El :attribute no debe ser mayor que :max caracteres.',
        'array' => 'El :attribute no debe tener mas de :max articulos.',
    ],
    'mimes' => 'El :attribute debe ser un archivo de tipo :values.',
    'mimetypes' => 'El :attribute debe ser un archivo de tipo :values.',
    'min' => [
        'numeric' => 'El :attribute debe ser por lo menos :min.',
        'file' => 'El :attribute debe ser por lo menos :min kilobytes.',
        'string' => 'El :attribute debe ser por lo menos :min caracteres.',
        'array' => 'El :attribute debe tener por lo menos :min articulos.',
    ],
    'not_in' => 'El :attribute seleccionado no es valido.',
    'not_regex' => 'El formato :attribute no es valido.',
    'numeric' => 'El :attribute debe ser a numero.',
    'password' => 'The password is incorrect.',
    'present' => 'El campo :attribute debe estar presente.',
    'regex' => 'El formato :attribute no es valido.',
    'required' => 'El campo :attribute es requerido.',
    'required_if' => 'El campo :attribute es requerido cuando :other es :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'El campo :attribute es requerido cuando :values esta presente.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'El campo :attribute es requerido cuando :values no esta presente.',
    'required_without_all' => 'El campo :attribute es requerido cuando ninguno de los :values estan presentes.',
    'same' => 'El :attribute y :other deben coincidir.',
    'size' => [
        'numeric' => 'El :attribute debe ser :size.',
        'file' => 'El :attribute debe ser :size kilobytes.',
        'string' => 'El :attribute debe ser :size caracteres.',
        'array' => 'El :attribute debe contener :size articulos.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'El :attribute debe ser una zona valida.',
    'unique' => 'El :attribute ya ha sido tomado.',
    'uploaded' => 'El :attribute no se pudo cargar.',
    'url' => 'El formato :attribute no es valido.',
    'uuid' => 'The :attribute must be a valid UUID.',

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
            'regex' => 'La contraseña tiene los siguiente criterios que debe seguir:
                        <li>Mínimo 8 caracteres</li>
                        <li>Mayúscula (A – Z)</li>
                        <li>Minúscula (a – z)</li>
                        <li>Dígito (0 – 9)</li>\',',
        ],
    ],

    'available'            => 'No hay entrada disponible en los a. de config. - Por favor inserte :attribute',
    'comma_separated_hostnames_or_ips'  =>  'This is not a comma separated list of hostnames and/or IP addresses',
    'creditor_id'          => ':attribute no es válido.',
    'docsis'               => ':attribute',
    'geopos'               => 'No valid position. Please make sure, there are no white spaces and the position is given in the format ´Longitude,Latitude´. The precision of the position should be limited to 11 digits after the comma.',
    'hostname_or_ip'       => 'This is neither a hostname nor an IP address',
    'invalid_input'        => 'Entrada no válida – por favor corrija los siguientes errores.',
    'ip_in_range'          => 'La direccion IP no esta dentro del rango especificado anteriormente',
    'ip_larger'            => 'La direccion IP debe tener una cifra mayor debido a lo especificado en campos anteriores',
    'mac'                  => 'El :attribute debe ser una direccion MAC de la forma: aa:bb:cc:dd:ee:ff',
    'netmask'              => 'No es una netmask correcta',
    'not_null'             => 'Este campo tiene que ser establecido (no 0)',
    'null_if'              => 'Tiene que ser cero',
    'period'               => ':attribute tiene un formato invalido',
    'product' 			=> '¡Los tipos de productos deben escribirse como en el mensaje de ayuda y separados por comas!',

    'needed_depending_on_salutation' => 'El campo :attribute es necesario para el saludo elegido.',
    'reassign_phonenumber_to_mta_fail' => 'Asignación de número de teléfono a MTA :id no permitido',

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
        'address' => 'dirección',
        'age' => 'edad',
        'available' => 'Disponible',
        'birthday' => 'Fecha nacimiento',
        'city' => 'ciudad',
        'company' => 'Empresa',
        'content' => 'contenido',
        'country' => 'país',
        'date' => 'fecha',
        'day' => 'día',
        'description' => 'descripción',
        'docsis' => 'DOCSIS',
        'email' => 'correo electrónico',
        'excerpt' => 'extracto',
        'first_name' => 'nombre',
        'firstname' => 'Nombres',
        'gender' => 'género',
        'hour' => 'hora',
        'last_name' => 'apellido',
        'minute' => 'minuto',
        'mobile' => 'móvil',
        'month' => 'mes',
        'name' => 'nombre',
        'lastname' => 'Apellido',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de la contraseña',
        'phone' => 'teléfono',
        'salutation' => 'Tratamiento',
        'second' => 'segundo',
        'sex' => 'sexo',
        'size' => 'Tamaño',
        'time' => 'hora',
        'title' => 'título',
        'users_ids' => 'Usuarios asignados',
        'username' => 'usuario',
        'year' => 'año',
        'zip' => 'Código Postal',
    ],
];
