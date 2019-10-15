<?php

class AddDefaultGenieAcsParameterProvisionsAndPresets extends BaseMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $url = 'http://'.\Modules\ProvBase\Entities\ProvBase::first()['provisioning_server'];

        // add virtual parameter for GET/POST and possibility for refreshing it
        foreach ([['url' => "$url:7557/virtual_parameters/SerialNumber", 'request' => 'PUT', 'data' => 'var sn = declare("DeviceID.SerialNumber", {value: Date.now()}); return {writable: false, value: [sn.value[0], "xsd:string"]};'],
            ['url' => "$url:7557/provisions/Refresh_VParams", 'request' => 'PUT', 'data' => 'declare("VirtualParameters.*", {value: Date.now()});'], ] as $value) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                        CURLOPT_URL => $value['url'],
                        // CURLOPT_RETURNTRANSFER =>  true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_CUSTOMREQUEST => $value['request'],
                        CURLOPT_POSTFIELDS => $value['data'],
                    ]);

            curl_exec($ch);
            curl_close($ch);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
