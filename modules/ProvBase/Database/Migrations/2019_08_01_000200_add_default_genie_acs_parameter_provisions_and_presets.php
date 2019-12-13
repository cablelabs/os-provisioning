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
        $url = 'http://localhost:7557';

        // wait max. 2 minutes for genieacs-nbi to get up and running
        $i = 0;
        while ($i < 120) {
            try {
                file_get_contents("$url/provisions/");
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
        ];

        // add virtual parameter for GET/POST and possibility for refreshing it
        foreach ($requests as $request) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => "$url/{$request['url']}/",
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
