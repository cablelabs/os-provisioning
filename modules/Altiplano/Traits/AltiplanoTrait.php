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

namespace Modules\Altiplano\Traits;

trait AltiplanoTrait
{
    public function callApi($url, $args = [])
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => array_key_exists('customRequest', $args) ? $args['customRequest'] : 'POST',
            CURLOPT_POSTFIELDS => array_key_exists('data', $args) ? $args['data'] : null,
            CURLOPT_HTTPHEADER => array_key_exists('headers', $args) ? $args['headers'] : [],
        ]);

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return [
            'value' => json_decode($result, true),
            'success' => $status == 200,
        ];
    }

    public function altiplanoGetToket()
    {
        $response = $this->callApi('https://altiplano.kfsb.ch/nokiasolution-altiplano-ac/rest/auth/login', [
            'customRequest' => 'POST',
            'headers' => [
                'Authorization: Basic bm1zcHJpbWU6ZjZuV0dHcF1ASg==',
            ],
        ]);

        return $response['success'] ? $response['value']['accessToken'] : null;
    }

    public function altiplanoAvailableFiberNames()
    {
        $response = $this->callApi('https://altiplano.kfsb.ch/nokiasolution-altiplano-ac/rest/restconf/operations/ibn:search-intents', [
            'customRequest' => 'POST',
            'headers' => [
                "Authorization: Bearer $this->token",
                'content-type: application/yang-data+json',
            ],
            'data' => '{"ibn:search-intents":{"filter":{"intent-type-list":[{"intent-type":"fiber"}]}}}',
        ]);

        return $response;
    }

    public function altiplanoCreateOnt($args = [])
    {
        $target = array_key_exists('target', $args) ? $args['target'] : null;
        $ontType = array_key_exists('ont_type', $args) ? $args['ont_type'] : null;
        $fiberName = array_key_exists('fiber_name', $args) ? $args['fiber_name'] : null;
        $expectedSerialNumber = array_key_exists('expected_serial_number', $args) ? $args['expected_serial_number'] : null;

        $response = $this->callApi('https://altiplano.kfsb.ch/nokiasolution-altiplano-ac/rest/restconf/data/ibn:ibn', [
            'customRequest' => 'POST',
            'headers' => [
                "Authorization: Bearer $this->token",
                'content-type: application/yang-data+json',
            ],
            'data' => '{"ibn:intent":{"intent-type":"ont","target":"'.$target.'","intent-specific-data":{"ont:ont":{"ont-type":"'.$ontType.'","pon-type":"xgs","fiber-name":"'.$fiberName.'","expected-serial-number":"'.$expectedSerialNumber.'","onu-service-profile":"default","auto":"","uni-service-configuration":[{"uni-id":"10GE","service-profile":"default"}],"pots-service-profile":"","active-software":"","passive-software":""}}}}',
        ]);

        return $response;
    }

    public function createSubscription($args = [])
    {
        $target = array_key_exists('target', $args) ? $args['target'] : null;
        $userDeviceName = array_key_exists('user_device_name', $args) ? $args['user_device_name'] : null;
        $cVlanId = array_key_exists('c_vlan_id', $args) ? $args['c_vlan_id'] : null;
        $serviceProfile = array_key_exists('service_profile', $args) ? $args['service_profile'] : null;

        $response = $this->callApi('https://altiplano.kfsb.ch/nokiasolution-altiplano-ac/rest/restconf/data/ibn:ibn', [
            'customRequest' => 'POST',
            'headers' => [
                "Authorization: Bearer $this->token",
                'content-type: application/yang-data+json',
            ],
            'data' => '{"ibn:intent":{"target":"'.$target.'","intent-type":"l2-user","intent-specific-data":{"l2-user:l2-user":{"user-device-name":"'.$userDeviceName.'","uni-id":"LAN1","q-vlan-id":"","c-vlan-id":"'.$cVlanId.'","service-profile":"'.$serviceProfile.'"}},"intent-type-version":"9","required-network-state":"active"}}',
        ]);

        return $response;
    }
}
