<?php

namespace App\Console\Commands;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RefreshBearerTokenCommand extends Command implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'nms:refresh-bearer-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the bearer token for the openTSDB API';

    protected $url;

    protected $grafanaUserName;

    protected $grafanaPassword;

    protected $grafanaDataSourceName = 'Altiplano';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->url = 'http://localhost:3001/api/datasources';
        $this->grafanaUserName = config('provmon.grafanaUsername');
        $this->grafanaPassword = config('provmon.grafanaPassword');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = $this->getNewToken();

        $jsonData = $this->getDataSourceJson();
        $jsonData['jsonData'] = [
            'httpHeaderName1' => 'Authorization',
            'tlsSkipVerify' => true,
        ];
        $jsonData['secureJsonData'] = ['httpHeaderValue1' => 'Bearer '.$token];

        $this->refreshBearerToken($jsonData);
    }

    protected function getDataSourceJson()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.'/name/'.$this->grafanaDataSourceName);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->grafanaUserName:$this->grafanaPassword");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    protected function refreshBearerToken($jsonData)
    {
        $jsonString = json_encode($jsonData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.'/'.$jsonData['id']);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->grafanaUserName:$this->grafanaPassword");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        if (Str::contains($response, 'Datasource updated')) {
            echo 'Data source name updated successfully.';
            dd([$response]);
        } else {
            echo 'Failed to update data source name.';
        }
    }

    protected function getNewToken()
    {
        $username = env('AUTH_USERNAME');
        $password = env('AUTH_PASSWORD');
        $client_id = 'ALTIPLANO';
        $client_secret = env('AUTH_CLIENT_SECRET');
        $auth_url = env('AUTH_URL');

        $auth = base64_encode("$username:$password");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'username' => $username,
            'password' => $password,
            'grant_type' => 'password',
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic $auth",
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        curl_close($ch);

        $access_token = '';

        if ($response) {
            preg_match('/access_token":"([^"]+)/', $response, $matches);
            if (isset($matches[1])) {
                $access_token = $matches[1];
            }
        }

        return $access_token;
    }
}
