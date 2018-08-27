<?php

return [
 /*
  * Authentication and Base
  */
    'translate'					=> 'You can help translating NMS PRIME at',
    'assign_role'					=> 'Asigna uno o más roles a este usuario. Los usuarios sin Rol no pueden usar el NMS porque no tienen Permisos.',
    'assign_users'					=> 'Asigna uno o más usuarios a este rol. Los cambios realizados aquí no son visibles en el GuiLog del usuario.',
    'assign_rank'					=> 'El rango de un rol determina la capacidad de editar otros usuarios. <br \>Puedes asignar valores de 0 a 100. (más alto es mejor). <br \>Si un usuario tiene más de un rol, se usa el rango más alto . <br \>Si se establece la capacidad de actualizar usuarios, el rango también se verifica. Solo si el rango del editor es mayor, se concede permiso. Además, al crear o actualizar usuarios, solo se pueden asignar roles con rango igual o inferior.',
    'All abilities'					=> 'Esta habilidad permite todas las solicitudes de autorización, excepto las habilidades, que están explícitamente prohibidas. Esto es principalmente una habilidad de ayuda. La prohibición está deshabilitada, porque solo se permiten las habilidades marcadas. Si esta habilidad no está marcada, debes establecer todas las habilidades a mano. Si cambias esta habilidad, cuando se establecen muchas otras habilidades, tomará hasta 1 minuto aplicar todos los cambios.',
    'View everything'			=> 'Esta capacidad permite ver todas las páginas. La prohibición está inhabilitada porque hace que el NMS no se pueda usar. Esto es principalmente una capacidad de ayuda para los invitados o usuarios con privilegios muy bajos.',
    'Use api'					=> 'Esta capacidad permite o prohíbe el acceso a las rutas API con "Basic Auth" (el correo electrónico se usa como nombre de usuario).',
    'See income chart'			=> 'Esta capacidad permite o prohíbe ver la tabla de ingresos en el panel de control.',
    'View analysis pages of modems'	=> 'Esta capacidad permite o prohíbe el acceso a las páginas de análisis de un módem.',
    'View analysis pages of cmts' => 'Esta capacidad permite o prohíbe el acceso a las páginas de análisis de un CMTS.',
    'Download settlement runs'	=> 'Esta capacidad permite o prohíbe la descarga de ejecuciones de liquidación. Esta capacidad no tiene ningún impacto si está prohibido administrar ejecuciones de liquidación.',
 /*
  * Index Page - Datatables
  */
    'SortSearchColumn'				=> 'Esta Columna no puede ser examinada u ordenada.',
    'PrintVisibleTable'				=> 'Imprime la tabla mostrada. Si la tabla esta filtrada, asegurarse de seleccionar la opcion \\"Todo\\" para mostrar todo. Espere algunos segundos.',
    'ExportVisibleTable'			=> 'Exporta la tabla seleccionada. Si la tabla esta filtrada, asegurarse de seleccionar la opcion \\"Todo\\" para mostrar todo. Espere algunos segundos.',
    'ChangeVisibilityTable'			=> 'Seleccione las columnas que deberian ser visibles.',

    // GlobalConfig
    'ISO_3166_ALPHA-2'				=> 'ISO 3166 ALPHA-2 (dos caracteres, p.e. “US”). Usado en formularios de direccion para especificar el pais.',

 /*
  *	MODULE: BillingBase
  */
    //BillingBaseController
    'BillingBase_cdr_offset' 		=> "ADVERTENCIA: incrementar esto mientras se tiene datos de Acuerdos ocasiona sobrescribir CDRs la siguiente ejecucion - Este seguro de guardar/renombrar el historial!\n\nEjemplo: Asignar a 1 si los Registros de Datos de Llamada de Junio, pertenecen a las Facturas de Julio, 0 si este es del mismo mes, 2 si RDLs de Enero pertenecen a las Facturas de Marzo.",
    'BillingBase_cdr_retention' 	=> 'Meses que Registros de Datos de Llamada Months that Call Data Records may/have to be kept save',
    'BillingBase_extra_charge' 		=> 'Beneficio adicional al precio de compra. Solo cuando no es calculado mediante el proveedor!',
    'BillingBase_fluid_dates' 		=> 'Marque esta casilla si quiere aniadir tarifas con fechas de inicio y/o plazo dudosas. Si se marcaron dos nuevos casilleros (Valido desde, Valido hasta) aparecera en la pagina editar/crear Articulo. Revise sus mensajes de ayuda para explicaciones adicionales!',
    'BillingBase_InvoiceNrStart' 	=> 'Contador de Cifras de Factura empieza cada nuevo anio con esta cifra',
    'BillingBase_ItemTermination'	=> 'Permitir a los Clientes solo cancelar productos reservados el ultimo dia del mes',
    'BillingBase_MandateRef'		=> "Un Formulario puede ser construido con columnas SQL de las tablas contrato/mandato - posibles campos: \n",
    'BillingBase_showAGs' 			=> 'Adiciona una lista seleccionada con personas contactadas a la pagina de contrato. La lista tiene que ser almacenada en un directorio Alamacenamiento apropiado - revise el codigo fuente!',
    'BillingBase_SplitSEPA'			=> 'Sepa Transfers are split to different XML-Files dependent of their transfer type',

    //CompanyController
    'Company_Management'			=> 'Lista de nombres separada por comas',
    'Company_Directorate'			=> 'Lista de nombres separada por comas',
    'Company_TransferReason'		=> 'Formulario desde todas las clases de Factura de campos de datos primarios - Cifra de Contrato y Cifra de Factura es por defecto',
    'conn_info_template' 			=> 'Plantilla Tex es usada para Crear Informacion de Conexion en la Pagina de Contrato para un Cliente',

    //CostCenterController
    'CostCenter_BillingMonth'		=> 'Contabilizacion para articulos de pago anual - corresponde al mes por el cual las facturas son creadas. Por Defecto: 6 (Junio) - si no es establecido. Sea cuidadoso de no olvidar algun pago al momento de modificarse!',

    //ItemController
    'Item_ProductId'				=> 'Todos los campos ademas del Ciclo de Facturacion tienen que ser despejados antes de algun cambio! De otra manera, los articulos no podran ser guardados en la mayoria de los casos',
    'Item_ValidFrom'				=> 'Para Pagos de Una Vez los campos pueden ser usados para dividir pagos - Solo YYYY-MM es considerado entonces!',
    'Item_ValidFromFixed'			=> 'Marcado por defecto! Desmarque si la tarifa deberia quedar inactiva cuando una fecha de inicio es alcanzada (p.ej. si el cliente esta esperando por transferencia de numero telefonico). La tarifa no sera iniciada y no sera cargada hasta que active la casilla. Luego, la fecha de inicio sera incrementada cada dia un dia despues de alcanzar la fecha de inicio. Nota: La fecha no es actualizada por ordenes externas (p.ej. desde proveedor de telefonia).',
    'Item_ValidToFixed'				=> 'Marcado por defecto! Desmarcar si la fecha de pazo es desconocida. Si es desmarcada, la tarifa no acabara y sera cargada hasta que active la casilla. Luego, cuando la fecha de plazo es alcanzada, sera incrementada cada dia en un dia. Nota: La fecha no es actualizada por ordenes externas (p.ej. desde proveedor de telefonia).',
    'Item_CreditAmount'				=> 'Cantidad Neta a ser acreditada al Cliente. Cuidado: una cantidad negativa viene a ser un debito!',

    //ProductController
    'Product_maturity' 				=> 'Periodo/Tiempo de ejecucion. P.ej. 14D (14 dias), 3M (3 meses), 1Y (1 anio)',
    'Product_Name' 					=> 'Para creditos es posible de asignar un Tipo aniadiendo el tipo de nombre al Nombre del Credito - p.ej.: \'Credito de Dispositivo\'',
    'Product_Number_of_Cycles' 		=> 'Ten cuidado!: para todos los productos pagados repetidos, el precio aplica para cada deuda, para productos pagados de una, el Precio es dividido por el numero de ciclos',
    'Product_Type'					=> 'Todos los campos ademas del Ciclo de Facturacion tienen que ser despejados antes de algun cambio! De otra manera, los productos no seran guardados en la mayoria de los casos',

    //SalesmanController
    'Salesman_ProductList'			=> 'Aniadir todos los tipos de Producto por los cuales se obtiene comision - posible: ',

    // SepaMandate
    'sm_cc' 						=> 'Si un centro de coste es asignado, solo productos relacionados al mismo seran cargados a la cuenta. Deje este campo vacio si todos los cargos que no puedan ser asignados a otro mandado-SEPA con especifico centro de coste, deben ser debitado a esta cuenta. Nota: Se asume que todos los costos emergentes que no pueden ser asignados a algun mandado-SEPA, seran pagados en efectivo!',
    'sm_recur' 						=> 'Activar si ya han habido transacciones de esta cuenta, antes de la creacion de este mandado. Establece el estado a recurrente. Nota: Esta etiqueta solo es considerada en la primera transaccion!',

    //SepaAccountController
    'SepaAccount_InvoiceHeadline'	=> 'Remplaza el Encabezado en Facturas creadas para este Centro de Coste',
    'SepaAccount_InvoiceText'		=> 'El Texto de los cuatro Campos-\'Texto de Factura\' independientes, es automaticamente escogido dependiendo del cargo total y del Mandado SEPA, ademas es establecido en la Factura para el Cliente apropiada. Es posible de usar todos los datos de campo primarios de la Clase Factura como referente en la forma de {fieldname} para construir un tipo de plantilla. Estos son reemplazados por el valor actual de la Factura.',
    'tex_template' 					=> 'Plantilla TeX',

    // SettlementrunController
    'settlement_verification' 		=> 'Si es activada, no es posible de repetir el Acuerdo. Las Facturas del Cliente son solo visibles cuando esta casilla esta marcada.',

 /*
  *	MODULE: HfcReq
  */
    'netelementtype_reload' 		=> 'En Segundos. Cero para desactivar auto-cargado. Decimales disponibles.',
    'netelementtype_time_offset' 	=> 'En Segundos. Decimales disponibles.',
    'undeleteables' 				=> 'Red & Grupo no pueden ser cambiados debido a que tienen relevacia en todos los Diagramas Entidad Relacion',

 /*
  *	MODULE: HfcSnmp
  */
    'mib_filename' 					=> 'El Nombre de Archivo esta compuesto por un nombre MIB & Revision. Si ya existe un Archivo identico, no es posible el crearlo otra vez.',
    'oid_link' 						=> 'Ir a configuraciones de OID',
    'oid_table' 					=> 'INFO: Este Parametro pertenece a la Tabla-OID. Si usted agrega/especifica SubOIDs y/o indices, solo estos son considerados para el snmpwalk. Ademas del mejor Resumen, este puede dramaticamente acelerar la Creacion de la Vista de Control para el correspondiente Elemento de Red.',
    'parameter_3rd_dimension' 		=> 'Marque esta casilla si este Parametro pertenece a una Vista de Control extra detras de un Elemento de la SnmpTable.',
    'parameter_diff' 				=> 'Marque esto si solo la Diferencia de los valores actuales a los ultimos consultados debe ser mostrado.',
    'parameter_divide_by' 			=> 'Hacer este Valor/Parametro porcentual comparado a los valores agregados de los siguientes OIDs que son consultados por el snmpwalk actual, tambien. En un primer lugar, esto solo funciona en SubOIDs de las tablas especificadas! El Calculo es Realizado despues de que la Diferencia es calculada en caso de Parametros-Diferencia.',
    'parameter_indices' 			=> 'Especificar una Lista separada por comas, de todas las Filas de las Tablas que el Snmp equivaldra.',
    'parameter_html_frame' 			=> 'Asignar este parametro a un especifico frame (parte de la pagina). No influye en SubOIDs en Tablas.',
    'parameter_html_id' 			=> 'Agregando un ID, usted puede ordenar este parametro en secuencia de otros parametros. Puede cambiar el orden de las columnas en las tablas, configurando el html id Sub-Params.',

 /*
  *	MODULE: ProvBase
  */
    'rate_coefficient'				=> 'La Maxima Tarifa Sostenida sera multiplicada por este valor para otorgar al usuario mas (> 1.0) rendimiento que el suscrito.',
    //ModemController
    'Modem_NetworkAccess'			=> 'Acceso a la Red para CPEs. (MTAs no son considerados y iran siempre online cuando las demas configuraciones sean correctas). Cuidado: Con el Modulo-Facturacion, esta casilla sera siempre sobrescrita diariamente si la tarifa cambia.',
    'Modem_InstallationAddressChangeDate'	=> 'En caso de (fisico) reubicacion del modem: Agregar fecha de inicio para la nueva direccion ahi. Si es solo lectura, hay una orden de cambio de direccion pendiente en Envia.',
    'Modem_GeocodeOrigin'			=> 'De donde vienen los datos geocode? Si se establece a "n/a", la direccion no podra ser geocoded para cualquier API. Sera establecido a su nombre en cambios manuales de geodata.',
    'contract_number' 				=> 'Atencion - Contrasena del Cliente es cambiado automaticamente cuando se cambia este campo!',
    'mac_formats'					=> "Formatos permitidos (case-insensitive):\n\n1) AA:BB:CC:DD:EE:FF\n2) AABB.CCDD.EEFF\n3) AABBCCDDEEFF",
    'fixed_ip_warning'				=> 'Usar una IP fija es altamente no recomendado, ya que pierde la habilidad de mover modems y sus CPEs libremente entre CMTSes. Envez de dar una IP fija al cliente, deberan ser provistos del hostname, el cual no cambiara.',
    'modem_update_frequency'		=> 'Este campo se actualiza una vez al día.',

 /*
  *	MODULE: ProvVoip
  */
    //PhonenumberManagementController
    'PhonenumberManagement_CarrierIn' => 'En puerto entrante: establecer al Telco anterior.',
    'PhonenumberManagement_CarrierInWithEnvia' => 'En puerto entrante: establecer al Telco anterior. Si hubiese una nueva cifra, se establece a EnviaTEL',
    'PhonenumberManagement_EkpIn' => 'En puerto entrante: establecer al Telco.',
    'PhonenumberManagement_EkpInWithEnvia' => 'En puerto entrante: establecer al Telco anterior. Si hubiese una nueva cifra, se establece a EnviaTEL',
    'PhonenumberManagement_TRC' => 'Solo para su conocimiento. Los cambios reales tienen que ser realizadas en su Telco.',
    'PhonenumberManagement_TRCWithEnvia' => 'Si se cambia aqui, tambien tiene que ser enviado a Envia (Actualizar su cuenta VoIP).',
    'PhonenumberManagement_Autogenerated' => 'Esta gestion ha sido creada automaticamente. Por favor, verifique/cambie valores, entonces desmarque esta casilla.',
/*
  * MODULE VoipMon
  */
    'mos_min_mult10' 				=> 'Minimal Mean Opionion Score experimentado durante una llamada',
    'caller' 						=> 'Direccion de Llamada de Emisor a Receptor',
    'a_mos_f1_min_mult10' 			=> 'Minimal Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 50ms',
    'a_mos_f2_min_mult10' 			=> 'Minimal Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 200ms',
    'a_mos_adapt_min_mult10' 		=> 'Minimal Mean Opionion Score experimentado durante una llamada por un adaptive jitter buffer de 500ms',
    'a_mos_f1_mult10' 				=> 'Average Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 50ms',
    'a_mos_f2_mult10' 				=> 'Average Mean Opionion Score experimentado durante una llamada por un fixed jitter buffer de 200ms',
    'a_mos_adapt_mult10' 			=> 'Average Mean Opionion Score experimentado durante una llamada por un adaptive jitter buffer de 500ms',
    'a_sl1' => 'Numero de paquetes experimentando una perdida de paquete consecutiva durante una llamada',
    'a_sl9' => 'Numero de paquetes experimentando nueve perdidas de paquete consecutivas durante una llamada',
    'a_d50' => 'Numero de paquetes experimentando una variacion en el retraso del paquete (p.ej. Jitter) entre 50ms y 70ms',
    'a_d300' => 'Numero de paquetes experienciando un retraso en la variacion del paquete (p.ej. Jitter) mayor a 300ms',
    'called' => 'Direccion de Llamada de Receptor a Emisor',
/*
 * Module Ticketsystem
 */
    'assign_user' => ' Permitido de asignar un usuario a un ticket',
 ];
