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

        // wait max. 2 minutes for genieacs-nbi to get up and running
        $i = 0;
        while ($i < 120) {
            try {
                file_get_contents("$url:7557/provisions/");
                break;
            } catch (Exception $e) {
                sleep(1);
                $i++;
            }
        }

        $requests = [
            [
                'url' => 'objects/0',
                'data' => '{}',
            ],
            [
                'url' => 'objects/1',
                'data' => '{}',
            ],
            [
                'url' => 'objects/2',
                'data' => '{}',
            ],
            [
                'url' => 'objects/3',
                'data' => '{}',
            ],
            [
                'url' => 'objects/4',
                'data' => '{}',
            ],
            [
                'url' => 'provisions/Refresh_VParams',
                'data' => 'declare("VirtualParameters.*", {value: Date.now()});',
            ],
            [
                'url' => 'virtual_parameters/IP',
                'data' => '
                    let ip = \'0.0.0.0\';
                    let IGDeviceIp = declare("InternetGatewayDevice.ManagementServer.ConnectionRequestURL", {value: Date.now()});
                    let deviceIp = declare("Device.ManagementServer.ConnectionRequestURL", {value: Date.now()});

                    if (deviceIp.size) {
                        for (let p of deviceIp) {
                            if (p.value[0]) {
                                ip = p.value[0];
                                break;
                            }
                        }
                    } else if (IGDeviceIp.size) {
                        for (let p of IGDeviceIp) {
                            if (p.value[0]) {
                                ip = p.value[0];
                                break;
                            }
                        }
                    }

                    return { writable: true, value: [ip, "xsd:string"] };',
            ],
            [
                'url' => 'virtual_parameters/SerialNumber',
                'data' => '
                    let sn = 0;
                    let snDevice = declare("DeviceID.SerialNumber", {value: Date.now()});
                    let snIGD = declare("InternetGatewayDevice.DeviceInfo.SerialNumber", {value: Date.now()});

                    if (snDevice.size) {
                        for (let p of snDevice) {
                            if (p.value[0]) {
                                sn = p.value[0];
                                break;
                            }
                        }
                    } else if (snIGD.size) {
                        for (let p of snIGD) {
                            if (p.value[0]) {
                                sn = p.value[0];
                                break;
                            }
                        }
                    }

                    return { writable: true, value: [sn, "xsd:string"] };',
            ],
        ];

        // add virtual parameter for GET/POST and possibility for refreshing it
        foreach ($requests as $request) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => "$url:7557/{$request['url']}/",
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $request['data'],
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
