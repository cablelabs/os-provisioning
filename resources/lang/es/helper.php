<?php

return [
    /*
  * Authentication and Base
  */
    'translate'                 => 'Puedes ayudar a traducir NMS PRIME en',
    'assign_role'                   => 'Asigna uno o más roles a este usuario. Los usuarios sin Rol no pueden usar el NMS porque no tienen Permisos.',
    'assign_users'                  => 'Asigna uno o más usuarios a este rol. Los cambios realizados aquí no son visibles en el GuiLog del usuario.',
    'assign_rank'                   => 'El rango de una función determina la posibilidad de editar otros usuarios. <br \>puede asignar valores de 0 a 100. (mayor es mejor). <br \>si un usuario tiene más de una función, se utiliza el rango más alto. <br \>si se establece la posibilidad de actualizar los usuarios, la fila también se comprueba. Solamente si el rango del editor es mayor, se concede el permiso. Además, al crear o actualizar los usuarios, pueden asignarse sólo funciones con rango igual o inferior.',
    'All abilities'                 => 'Esta habilidad permite todas las solicitudes de autorización, excepto las habilidades, que están explícitamente prohibidas. Esto es principalmente una habilidad de ayuda. La prohibición está deshabilitada, porque solo se permiten las habilidades marcadas. Si esta habilidad no está marcada, debes establecer todas las habilidades a mano. Si cambias esta habilidad, cuando se establecen muchas otras habilidades, tomará hasta 1 minuto aplicar todos los cambios.',
    'countryCode'               => 'ISO 3166 ALPHA-2 (dos caracteres). Necesario para la determinación de geocódigos. Si está vacío se utiliza el código de país por defecto especificado globalmente.',
    'View everything'           => 'Esta capacidad permite ver todas las páginas. La prohibición está inhabilitada porque hace que el NMS no se pueda usar. Esto es principalmente una capacidad de ayuda para los invitados o usuarios con privilegios muy bajos.',
    'Use api'                   => 'Esta capacidad permite o prohíbe el acceso a las rutas API con "Basic Auth" (el correo electrónico se usa como nombre de usuario).',
    'See income chart'          => 'Esta capacidad permite o prohíbe ver la tabla de ingresos en el panel de control.',
    'View analysis pages of modems' => 'Esta capacidad permite o prohíbe el acceso a las páginas de análisis de un módem.',
    'View analysis pages of netgw' => 'This ability allows or forbids to access the analysis pages of a NetGw.',
    'Download settlement runs'  => 'Esta capacidad permite o prohíbe la descarga de ejecuciones de liquidación. Esta capacidad no tiene ningún impacto si está prohibido administrar ejecuciones de liquidación.',
    /*
  * Index Page - Datatables
  */
    'SortSearchColumn'              => 'Esta Columna no puede ser examinada u ordenada.',
    'PrintVisibleTable'             => 'Imprime la tabla mostrada. Si la tabla esta filtrada, asegurarse de seleccionar la opcion \\"Todo\\" para mostrar todo. Espere algunos segundos.',
    'ExportVisibleTable'            => 'Exporta la tabla seleccionada. Si la tabla esta filtrada, asegurarse de seleccionar la opcion \\"Todo\\" para mostrar todo. Espere algunos segundos.',
    'ChangeVisibilityTable'         => 'Seleccione las columnas que deberian ser visibles.',

    // GlobalConfig
    'ISO_3166_ALPHA-2'              => 'ISO 3166 ALPHA-2 (dos caracteres, p.e. “US”). Usado en formularios de direccion para especificar el pais.',
    'PasswordReset'           => 'Esta propiedad define el intervalo de tiempo en días en los que los usuarios del panel de administración deben cambiar sus contraseñas. Si desea inhabilitar el mensaje de restablecimiento de contraseña, establezca el valor en 0.',

    //CompanyController
    'Company_Management'            => 'Lista de nombres separada por comas',
    'Company_Directorate'           => 'Lista de nombres separada por comas',
    'Company_TransferReason'        => 'Formulario desde todas las clases de Factura de campos de datos primarios - Cifra de Contrato y Cifra de Factura es por defecto',
    'conn_info_template'            => 'Plantilla Tex es usada para Crear Informacion de Conexion en la Pagina de Contrato para un Cliente',

    //CostCenterController
    'CostCenter_BillingMonth'       => 'Contabilizacion para articulos de pago anual - corresponde al mes por el cual las facturas son creadas. Por Defecto: 6 (Junio) - si no es establecido. Sea cuidadoso de no olvidar algun pago al momento de modificarse!',

    //ItemController
    'Item_ProductId'                => 'Todos los campos ademas del Ciclo de Facturacion tienen que ser despejados antes de algun cambio! De otra manera, los articulos no podran ser guardados en la mayoria de los casos',
    'Item_ValidFrom'                => 'Para Pagos de Una Vez los campos pueden ser usados para dividir pagos - Solo YYYY-MM es considerado entonces!',
    'Item_ValidFromFixed'           => 'Marcado por defecto! Desmarque si la tarifa deberia quedar inactiva cuando una fecha de inicio es alcanzada (p.ej. si el cliente esta esperando por transferencia de numero telefonico). La tarifa no sera iniciada y no sera cargada hasta que active la casilla. Luego, la fecha de inicio sera incrementada cada dia un dia despues de alcanzar la fecha de inicio. Nota: La fecha no es actualizada por ordenes externas (p.ej. desde proveedor de telefonia).',
    'Item_validTo'                  => 'Es posible especificar el número de meses aquí-por ejemplo, \' 12M \' durante 12 meses. Para los productos de pago mensuales sólo agregará el número de meses-por lo que la fecha de inicio 2018-05-04 será válida para 2019-05-04. Los artículos pagados individuales con el pago dividido serán cargados 12 veces-la fecha del final será 2019-04-31 entonces.',
    'Item_ValidToFixed'             => 'Marcado por defecto! Desmarcar si la fecha de pazo es desconocida. Si es desmarcada, la tarifa no acabara y sera cargada hasta que active la casilla. Luego, cuando la fecha de plazo es alcanzada, sera incrementada cada dia en un dia. Nota: La fecha no es actualizada por ordenes externas (p.ej. desde proveedor de telefonia).',
    'Item_CreditAmount'             => 'Cantidad Neta a ser acreditada al Cliente. Cuidado: una cantidad negativa viene a ser un debito!',

    //ProductController
    'product' => [
        'bundle'                => 'En tarifas agrupadas el tiempo mínimo de funcionamiento del contrato es determinado únicamente por la tarifa de Internet. De otra forma la última tarifa inicial valida (VoIP o Internet) dictamina esta fecha.',
        'maturity_min'          => 'Período mínimo de tarifa/tiempo de ejecución/término. Ejem. 14D (14 días), 3M (3 meses), 1Y (1 Año)',
        'maturity'              => 'Tariff period/runtime/term extension after the minimum runtime. <br> Will be automatically added when tariff was not canceled before period of notice. Default 1 month. If no maturity is given the end of term of the item is always set to the last day of the month. <br><br> E.g. 14D (14 days), 3M (three months), 1Y (one year)',
        'Name'                  => 'Para créditos es posible asignar un Tipo añadiendo el nombre del tipo al Nombre del Crédito. Ejem.: "Dispositivo de crédito"',
        'pod'                   => 'Por ejemplo 14D (14 días), 3M (tres meses), 1Y (un año)',
        'proportional'          => 'Activa esta casilla cuando los elementos que empiecen durante la ejecución actual de la liquidación se cargarán proporcionalmente. Por ejemplo, si un artículo de pago mensual comienza a mediados del mes, el cliente se cobrará sólo la mitad del precio completo en esta operación de liquidación.',
        'Type'                  => '¡Todos los campos además del ciclo de facturación deben ser limpiados antes de un cambio de tipo! De lo contrario, en la mayoría de los casos los productos no pueden ser guardados',
        'deprecated'            => 'Activate this checkbox if this product shall not be shown in the product select list when creating/editing items.',
    ],
    'Product_Number_of_Cycles'      => 'Ten cuidado!: para todos los productos pagados repetidos, el precio aplica para cada deuda, para productos pagados de una, el Precio es dividido por el numero de ciclos',

    //SalesmanController
    'Salesman_ProductList'          => 'Aniadir todos los tipos de Producto por los cuales se obtiene comision - posible: ',

    // SepaMandate
    'sm_cc'                         => 'Si un centro de coste es asignado, solo productos relacionados al mismo seran cargados a la cuenta. Deje este campo vacio si todos los cargos que no puedan ser asignados a otro mandado-SEPA con especifico centro de coste, deben ser debitado a esta cuenta. Nota: Se asume que todos los costos emergentes que no pueden ser asignados a algun mandado-SEPA, seran pagados en efectivo!',
    'sm_recur'                      => 'Activar si ya han habido transacciones de esta cuenta, antes de la creacion de este mandado. Establece el estado a recurrente. Nota: Esta etiqueta solo es considerada en la primera transaccion!',

    // SettlementrunController
    'settlement_verification'       => 'Las facturas del cliente son solo visibles cuando esta casilla esta activada. La casilla solo puede activarse si la ultima ejecución fue realizada para todas las cuentas SEPA (para no perder ningún cambio). Info: Si se activa no es posible repetir la ejecución de la liquidación.',

    /*
  * MODULE: Dashboard
  */
    'next'                          => 'Siguiente paso: ',
    'set_isp_name'                  => 'Configure el nombre del proveedor de servicio de red',
    'create_netgw'                  => 'Create first NetGw/CMTS',
    'create_cm_pool'                => 'Crear la primera pool de IP para los cable modem',
    'create_cpepriv_pool'           => 'Crear la primera pool privada de IP para CPE',
    'create_qos'                    => 'Crear el primer perfil QoS',
    'create_product'                => 'Crear el primer producto de facturación',
    'create_configfile'             => 'Crear el primer archivo de configuración',
    'create_sepa_account'           => 'Crear la primera cuenta SEPA',
    'create_cost_center'            => 'Crear el primer centro de costo',
    'create_contract'               => 'Crear el primer contrato',
    'create_nominatim'              => 'Establecer una dirección de correo electrónico (OSM_NOMINATIM_EMAIL) en /etc/nmsprime/env/global.env para habilitar geocodificación para módem',
    'create_nameserver'             => 'Establezca su servidor de nombres a 127.0.0.1 en /etc/resolv.conf y asegúrese de que no será sobreescrito a través de DHCP (vea DNS y PEERDNS en /etc/sysconfig/network-scripts/ifcfg-*)',
    'create_modem'                  => 'Crear el primer módem',

    /*
  * MODULE: HfcReq
  */
    'netelementtype_reload'         => 'En Segundos. Cero para desactivar auto-cargado. Decimales disponibles.',
    'netelementtype_time_offset'    => 'En Segundos. Decimales disponibles.',
    'undeleteables'                 => 'Red & Grupo no pueden ser cambiados debido a que tienen relevacia en todos los Diagramas Entidad Relacion',

    /*
  * MODULE: HfcSnmp
  */
    'mib_filename'                  => 'El Nombre de Archivo esta compuesto por un nombre MIB & Revision. Si ya existe un Archivo identico, no es posible el crearlo otra vez.',
    'oid_link'                      => 'Ir a configuraciones de OID',
    'oid_table'                     => 'INFO: Este Parametro pertenece a la Tabla-OID. Si usted agrega/especifica SubOIDs y/o indices, solo estos son considerados para el snmpwalk. Ademas del mejor Resumen, este puede dramaticamente acelerar la Creacion de la Vista de Control para el correspondiente Elemento de Red.',
    'parameter_3rd_dimension'       => 'Marque esta casilla si este Parametro pertenece a una Vista de Control extra detras de un Elemento de la SnmpTable.',
    'parameter_diff'                => 'Marque esto si solo la Diferencia de los valores actuales a los ultimos consultados debe ser mostrado.',
    'parameter_divide_by'           => 'Hacer este Valor/Parametro porcentual comparado a los valores agregados de los siguientes OIDs que son consultados por el snmpwalk actual, tambien. En un primer lugar, esto solo funciona en SubOIDs de las tablas especificadas! El Calculo es Realizado despues de que la Diferencia es calculada en caso de Parametros-Diferencia.',
    'parameter_indices'             => 'Especificar una Lista separada por comas, de todas las Filas de las Tablas que el Snmp equivaldra.',
    'parameter_html_frame'          => 'Asignar este parametro a un especifico frame (parte de la pagina). No influye en SubOIDs en Tablas.',
    'parameter_html_id'             => 'Agregando un ID, usted puede ordenar este parametro en secuencia de otros parametros. Puede cambiar el orden de las columnas en las tablas, configurando el html id Sub-Params.',

    /*
  * MODULE: ProvBase
  */
    'contract' => [
        'valueDate' => 'Día del mes para una fecha específica del valor. Anula la fecha de colección solicitada de la configuración global para este contrato en el SEPA XML.',
    ],
    'rate_coefficient'              => 'La Maxima Tarifa Sostenida sera multiplicada por este valor para otorgar al usuario mas (> 1.0) rendimiento que el suscrito.',
    'additional_modem_reset'        => 'Check if an additional button should be displayed, which resets the modem via SNMP without querying the NetGw.',
    'openning_new_tab_for_modem' => 'Marque la casilla para abrir la página de edición del módem en la nueva pestaña en vista topografía.',
    //ModemController
    'Modem_InternetAccess'          => 'Acceso a Internet para los CPEs (los MTAs no se consideran y siempre se conectarán cuando todas las demás configuraciones sean correctas). Tenga cuidado: Con el Módulo de facturación esta casilla se sobrescribirá por chequeo diario si cambia la tarifa.',
    'Modem_InstallationAddressChangeDate'   => 'En caso de (físico) reubicación del modem: agregar fecha de inicio para la nueva dirección ahí. Si es solo lectura, hay una orden de cambio de dirección pendiente en envia TEL.',
    'Modem_GeocodeOrigin'           => 'De donde vienen los datos geocode? Si se establece a "n/a", la direccion no podra ser geocoded para cualquier API. Sera establecido a su nombre en cambios manuales de geodata.',
    'netGwSupportState' => [
        'full-support' => 'More than 95% of netGw modules are listed as supported devices.',
        'restricted' => 'Between 80%-95% of netGw modules are listed as supported devices.',
        'not-supported' => 'Less than 80% of netGw modules are listed as supported devices.',
        'verifying' => 'Less than 80% of netGw modules are listed as supported devices, but the netGw is still within the verification period of 6 weeks',
    ],
    'contract_number'               => 'Atencion - Contrasena del Cliente es cambiado automaticamente cuando se cambia este campo!',
    'mac_formats'                   => "Formatos permitidos (case-insensitive):\n\n1) AA:BB:CC:DD:EE:FF\n2) AABB.CCDD.EEFF\n3) AABBCCDDEEFF",
    'fixed_ip_warning'              => 'Using fixed IP address is highly discouraged, as this breaks the ability to move modems and their CPEs freely among NetGws. Instead of telling the customer a fixed IP address they should be supplied with the hostname, which will not change.',
    'addReverse'                    => 'Para establecer un registro DNS inverso adicional, por ejemplo para servidores de correo',
    'modem_update_frequency'        => 'Este campo se actualiza una vez al día.',
    'modemSupportState' => [
        'full-support' => 'El módem está listado como un dispositivo compatible.',
        'not-supported' => 'El módem no aparece como un dispositivo compatible.',
        'verifying' => 'El módem aún no se encuentra como un dispositivo compatible, pero aún está dentro del período de verificación de 6 semanas.',
    ],
    'enable_agc'                    => 'Activar el control automático de ganancia para upstream.',
    'agc_offset'                    => 'Compensación del control automático de ganancia para el upstream en dB. (por defecto: 0.0)',
    'configfile_count'              => 'El número en paréntesis indica que tan seguido está siendo usado el archivo de configuración respectivo.',
    'has_telephony'                 => 'Activar si el cliente tendrá telefonía pero no tiene Internet. Esta bandera no puede ser utilizada para desactivar la telefonía en contratos con Internet. Por favor, elimine el MTA o desactive el número de teléfono para eso. Información: El ajuste influye en los parámetros de configuración NetworkAcess y MaxCPE de los módems - ver la pestaña de análisis de modems \'Configfile\'',
    'ssh_auto_prov'                 => 'Periodically run a script tailored to the OLT in order to automatically bring ONTs online.',

    /*
  * MODULE: ProvVoip
  */
    //PhonenumberManagementController
    'PhonenumberManagement_activation_date' => 'Se enviará al proveedor como fecha deseada, desencadena el estado activo del número de teléfono.',
    'PhonenumberManagement_deactivation_date' => 'Se enviará al proveedor como fecha deseada, desencadena el estado activo del número de teléfono.',
    'PhonenumberManagement_CarrierIn' => 'En puerto entrante: establecer al Telco anterior.',
    'PhonenumberManagement_CarrierInWithEnvia' => 'En puerto entrante: establecer al Telco anterior. Si hubiese una nueva cifra, se establece a envia TEL',
    'PhonenumberManagement_EkpIn' => 'En puerto entrante: establecer al Telco.',
    'PhonenumberManagement_EkpInWithEnvia' => 'En puerto entrante: establecer al Telco anterior. Si hubiese una nueva cifra, se establece a envia TEL',
    'PhonenumberManagement_TRC' => 'Solo para su conocimiento. Los cambios reales tienen que ser realizadas en su Telco.',
    'PhonenumberManagement_TRCWithEnvia' => 'Si se cambia aquí, también tiene que ser enviado a envia TEL (Actualizar su cuenta VoIP).',
    'PhonenumberManagement_ExternalActivationDate' => 'Fecha de activación en su proveedor.',
    'PhonenumberManagement_ExternalActivationDateWithEnvia' => 'Fecha de activación en envia TEL.',
    'PhonenumberManagement_ExternalDeactivationDate' => 'Fecha de desactivación en envia TEL.',
    'PhonenumberManagement_ExternalDeactivationDateWithEnvia' => 'Fecha de desactivación en envia TEL.',
    'PhonenumberManagement_Autogenerated' => 'Esta gestion ha sido creada automaticamente. Por favor, verifique/cambie valores, entonces desmarque esta casilla.',
    /*
  * MODULE VoipMon
  */
    'mos_min_mult10'                => 'Minimal Mean Opionion Score experimentado durante una llamada',
    'caller'                        => 'Direccion de Llamada de Emisor a Receptor',
    'a_mos_f1_min_mult10'           => 'Minimal Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 50ms',
    'a_mos_f2_min_mult10'           => 'Minimal Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 200ms',
    'a_mos_adapt_min_mult10'        => 'Minimal Mean Opionion Score experimentado durante una llamada por un adaptive jitter buffer de 500ms',
    'a_mos_f1_mult10'               => 'Average Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 50ms',
    'a_mos_f2_mult10'               => 'Average Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 200ms',
    'a_mos_adapt_mult10'            => 'Average Mean Opionion Score experimentado durante una llamada por un adaptive jitter buffer de 500ms',
    'a_sl1' => 'Numero de paquetes experimentando una perdida de paquete consecutiva durante una llamada',
    'a_sl9' => 'Numero de paquetes experimentando nueve perdidas de paquete consecutivas durante una llamada',
    'a_d50' => 'Numero de paquetes experimentando una variacion en el retraso del paquete (p.ej. Jitter) entre 50ms y 70ms',
    'a_d300' => 'Numero de paquetes experienciando un retraso en la variacion del paquete (p.ej. Jitter) mayor a 300ms',
    'called' => 'Direccion de Llamada de Receptor a Emisor',
    /*
 * Module Ticketsystem
 */
    'assign_user' => ' Permitido de asignar un usuario a un ticket',
    'mail_env'    => 'Siguiente: Establece tu Host/Usuario/Contraseña en /etc/nmsprime/env/global.env para permitir recibir Emails en Tickets',
    'noReplyMail' => 'La dirección de correo electrónico que debe ser mostrada como remitente, al crear/editar tickets. Esta dirección no tiene que existir. Por ejemplo: ejemplo@ejemplo.com',
    'noReplyName' => 'El nombre que debe mostrarse como remitente, al crear/editar tickets. Por ejemplo: NMS Prime',
    'ticket_settings' => 'Siguiente: Establecer nombre y dirección no responder en la página de configuración global.',
    'carrier_out'      => 'Código de operador del futuro socio contractual. Si se deja en blanco el número de teléfono se eliminará.',
];
