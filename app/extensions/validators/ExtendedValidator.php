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

namespace App\extensions\validators;

use Log;
use File;
use PHP_IBAN\IBAN;

/*
 * Our own ExtendedValidator Class
 *
 * IMPORTANT: add Validator::extend('xyz', 'ExtendedValidator@validateXyz'); to
 * ExtendedValidatorServiceProvider under app/Providers
 */
class ExtendedValidator
{
    public function notNull($attribute, $value, $parameters)
    {
        if ($value == '' || $value == '0' || $value == null || $value == '0000-00-00') {
            return false;
        }

        return true;
    }

    /*
     * Checks if value of a data field is zero when another field has specific values (declared in parameters)
     * NOTE: the fields name has to be replaced by prepare_rules-function of the Controller with the value of the data of this field
     */
    public function nullIf($attribute, $value, $parameters)
    {
        $data = $parameters[0];
        // dd($attribute, $value, $parameters);
        unset($parameters[0]);

        if (in_array($data, $parameters)) {
            if ($value == 0 || $value == null) {
                return true;
            }

            return false;
        }

        return true;
    }

    /*
     * MAC validation
     *
     */
    public function validateMac($attribute, $value, $parameters)
    {
        return boolval(filter_var($value, FILTER_VALIDATE_MAC));
    }

    /**
     * Check if ip ($value) is inside the ip range of a net
     *
     * @param 1 net_ip
     * @param 2 netmask
     * @return true if inside
     *
     * @author Nino Ryschawy
     */
    public function validateIpInRange($attribute, $value, $parameters)
    {
        preg_match('/\/\d{1,3}$/', $parameters[0], $match);

        if (! $match) {
            return false;
        }

        $ips = $this->getFirstAndLastIpFromPrefix($parameters[0]);

        return $this->isIpBetweenRange($value, $ips['first'], $ips['last']);
    }

    /**
     * See https://sizeofint.com/check-ip-is-in-range-between-start-ip-and-end-ip-ipv6-compatible-way-using-php/
     */
    public function isIpBetweenRange($ip, $startIp, $endIp)
    {
       return inet_pton($ip) >= inet_pton($startIp) && inet_pton($ip) <= inet_pton($endIp);
    }

    /**
     * @param string e.g. 100.64.0.0/24 or fd00::/48
     *
     * @return array|null
     */
    public function getFirstAndLastIpFromPrefix($prefix)
    {
        preg_match('/\/\d{1,3}$/', $prefix, $match);

        if (! $match) {
            return;
        }

        $version = \Modules\ProvBase\Entities\IpPool::getIpVersion($prefix);

        if ($version == '4') {
            return $this->getFirstAndLastIpv4FromPrefix($prefix);
        } elseif ($version == '6') {
            return $this->getFirstAndLastIpv6FromPrefix($prefix);
        }
    }

    /**
     * Get first and last IP from IPv6 subnet
     *
     * See https://stackoverflow.com/questions/4931721/getting-list-ips-from-cidr-notation-in-php
     */
    public function getFirstAndLastIpv4FromPrefix($prefix): array
    {
        $cidr = explode('/', $prefix);

        $range['first'] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
        $range['last'] = long2ip((ip2long($range['first'])) + pow(2, (32 - (int)$cidr[1])) - 1);

        return $range;
    }

    /**
     * Get first and last IP from IPv6 subnet
     *
     * This is definitely not the fastest way to do it!
     *
     * See https://stackoverflow.com/questions/10085266/php5-calculate-ipv6-range-from-cidr-prefix/10086404#10086404
     */
    public function getFirstAndLastIpv6FromPrefix($prefix): array
    {
        // Split in address and prefix length
        list($addr_given_str, $prefixlen) = explode('/', $prefix);

        // Parse the address into a binary string
        $addr_given_bin = inet_pton($addr_given_str);

        // Convert the binary string to a string with hexadecimal characters
        $addr_given_hex = bin2hex($addr_given_bin);

        // Overwriting first address string to make sure notation is optimal
        $addr_given_str = inet_ntop($addr_given_bin);

        // Calculate the number of 'flexible' bits
        $flexbits = 128 - $prefixlen;

        // Build the hexadecimal strings of the first and last addresses
        $addr_hex_first = $addr_given_hex;
        $addr_hex_last = $addr_given_hex;

        // We start at the end of the string (which is always 32 characters long)
        $pos = 31;
        while ($flexbits > 0) {
            // Get the characters at this position
            $orig_first = substr($addr_hex_first, $pos, 1);
            $orig_last = substr($addr_hex_last, $pos, 1);

            // Convert them to an integer
            $origval_first = hexdec($orig_first);
            $origval_last = hexdec($orig_last);

            // First address: calculate the subnet mask. min() prevents the comparison from being negative
            $mask = 0xf << (min(4, $flexbits));

            // AND the original against its mask
            $new_val_first = $origval_first & $mask;

            // Last address: OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
            $new_val_last = $origval_last | (pow(2, min(4, $flexbits)) - 1);

            // Convert them back to hexadecimal characters
            $new_first = dechex($new_val_first);
            $new_last = dechex($new_val_last);

            // And put those character back in their strings
            $addr_hex_first = substr_replace($addr_hex_first, $new_first, $pos, 1);
            $addr_hex_last = substr_replace($addr_hex_last, $new_last, $pos, 1);

            // We processed one nibble, move to previous position
            $flexbits -= 4;
            $pos -= 1;
        }

        // Convert the hexadecimal strings to a binary string
        $addr_bin_first = hex2bin($addr_hex_first);
        $addr_bin_last = hex2bin($addr_hex_last);

        // And create an IPv6 address from the binary string
        return [
            'first' => inet_ntop($addr_bin_first),
            'last' => inet_ntop($addr_bin_last),
        ];
    }

    /**
     * Check if ip ($value) is larger than the one specified in $parameters
     *
     * @author Nino Ryschawy
     *
     * @return bool
     */
    public function ipLarger($attribute, $value, $parameters)
    {
        return inet_pton($value) > inet_pton($parameters[0]);
    }

    /**
     * Converts an IPv6 string to a decimal value as string to be compareable
     * See https://www.samclarke.com/php-ipv6-to-128bit-int/
     *
     * @param string
     * @return mixed
     */
    private function inetPtoi($ip)
    {
        if (! function_exists('bcadd')) {
            throw new \Exception('PHP bcmath package must be installed!');
        }

        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return;
        }

        $parts = unpack('J*', inet_pton($ip));

        // convert any unsigned ints to signed from unpack
        foreach ($parts as &$part) {
            if ($part < 0) {
                $part = bcadd($part, '18446744073709551616');
            }
        }

        return bcadd($parts[2], bcmul($parts[1], '18446744073709551616'));
    }

    /**
     * Check if value is a valid IP (v4 or v6) with netmask in CIDR format
     */
    public function validateNet($attribute, $value, $parameters)
    {
        // Regex IPv4: regex:/^([0-9]{1,3}\.){3}([0-9]{1,3})\/\d{1,2}$/
        preg_match('/\/\d{1,3}$/', $value, $match);

        if (! $match) {
            return false;
        }

        $range = $this->getFirstAndLastIpFromPrefix($value);

        if (! $range) {
            return false;
        }

        if ($range['first'] != explode('/', $value)[0]) {
            return false;
        }

        return true;
    }

    /**
     * Validates Sepa Creditor Id
     * see: https://github.com/AbcAeffchen/SepaUtilities/blob/master/src/SepaUtilities.php
     */
    public function validateCreditorId($attribute, $value, $parameters)
    {
        $country_code = substr($value, 0, 2);

        // TODO: add more countries, improve by check against check digits
        $creditor_id_length = [
            'AT' => 18,         // Austria
            'BE' => 20,         // Belgium
            'DE' => 18,         // Germany
            'EE' => 20,         // Estonia
            'ES' => 16,
            'FR' => 13,
            'IT' => 23,
            'LU' => 26,         // Luxembourg
            'NL' => 19,
            'PT' => 13,         // Portugal
        ];

        if (strlen($value) != (isset($creditor_id_length[$country_code]) ? $creditor_id_length[$country_code] : 1000)) {
            return false;
        }

        return preg_match('#^[a-zA-Z]{2,2}[0-9]{2,2}([A-Za-z0-9]|[\+|\?|/|\-|:|\(|\)|\.|,|\']){3,3}([A-Za-z0-9]|[\+|\?|/|\-|:|\(|\)|\.|,|\']){1,28}$#', $value);
    }

    /*
     * Geoposition validation
     * see: http://stackoverflow.com/questions/7113745/what-regex-expression-will-check-gps-values
     */
    public function validateGeopos($attribute, $value, $parameters)
    {
        return preg_match('/(-?[\d]{1,3}\.[\d]{0,12},?){2}/', $value);
    }

    /*
     * DOCSIS configfile validation
     */
    public function validateDocsis($attribute, $value, $parameters)
    {
        if (! $value) {
            return true;
        }

        $arguments = func_get_args();

        /* Configfile */
        $device = $parameters[0];
        if ($device == null) {
            return false;
        }
        if ($device == 'tr069') {
            // configfile is csv, nothing to validate
            return true;
        }

        $dir = "/tftpboot/$device";
        $cf_file = $dir.'dummy-validator.conf';

        /*
         * Replace all {xyz} content
         */
        $rows = explode("\n", $value);
        $s = '';
        foreach ($rows as $row) {
            if (preg_match('/(string)/i', $row)) {
                $s .= "\n".preg_replace('/\\{[^\\{]*\\}/im', 'text', $row);
            } elseif (preg_match('/(ipaddress)/i', $row)) {
                $s .= "\n".preg_replace('/\\{[^\\{]*\\}/im', '1.1.1.1', $row);
            } else {
                $s .= "\n".preg_replace('/\\{.+\\}/im', '1', $row);
            }
        }

        /*
         * Write Dummy File and try to encode
         */
        $text = "Main\n{\n\t".$s."\n}";
        $ret = File::put($cf_file, $text);

        if ($ret === false) {
            exit('Error writing to file');
        }

        if ($device == 'cm') {
            Log::info("Validation: docsis -e $cf_file $dir/../keyfile $dir/dummy-validator.cfg");
            exec("rm -f $dir/dummy-validator.cfg && docsis -e $cf_file $dir/../keyfile $dir/dummy-validator.cfg 2>&1", $outs);
        } elseif ($device == 'mta') {
            Log::info("Validation: docsis -p $cf_file $dir/dummy-validator.cfg");
            exec("rm -f $dir/dummy-validator.cfg && docsis -p $cf_file $dir/dummy-validator.cfg 2>&1", $outs, $ret);    //return value is always 0
        }

        /*
         * Parse Errors - only one error is shown - subtract 3 from line nr
         */
        $report = $outs[0];
        preg_match('/[0-9]+$/', $report, $i);
        if (isset($i[0])) {
            $report = preg_replace('/[0-9]+$/', $i[0] - 3, $report);
        }
        // foreach ($outs as $out)
        // {
        // 	$report .= "\n$out";
        // }

        if (! file_exists("$dir/dummy-validator.cfg") && (isset($arguments[3]))) {
            // see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
            // when laravel calls the validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
            $validator = $arguments[3];
            $validator->setCustomMessages(['docsis' => $report]);

            return false;
        }

        return true;
    }

    public function validateDhcpConfig($attribute, $value, $parameters)
    {
        exec('/usr/sbin/dhcpd -t -cf /etc/dhcp-nmsprime/dhcpd.conf &>/dev/null', $out, $ret);

        return ! $ret;
    }

    // $value (field value) must only contain strings of product type enums - used on Salesman
    public function validateProductType($attribute, $value, $parameters)
    {
        $types = \Modules\BillingBase\Entities\Product::getPossibleEnumValues('type');

        $tmp = str_replace([',', '|', '/', ';'], ' ', $value);
        $prods = explode(' ', $tmp);

        foreach ($prods as $type) {
            // skip empty strings
            if (! $type) {
                continue;
            }
            if (! in_array($type, $types)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if given string (freetext) for phonebookentry is valid.
     *
     * @author Patrick Reichel
     */
    public function validatePhonebookString($attribute, $value, $parameters)
    {
        // see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
        // when laravel calls the validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
        $validator = \func_get_arg(3);

        // as of commit f2d4af6840abed1e2192855346d8f1af65758711 now empty strings are “null”
        // so we classify all kinds of empty strings as valid
        // this is save – if empty strings are not allowed another rule will strike; e.g. “required”
        if (! $value) {
            return true;
        }

        // for easier access and improved readability: get needed informations out of config
        $maxlen = \Modules\ProvVoip\Entities\PhonebookEntry::$config[$attribute]['maxlen'];
        $valid = str_split(\Modules\ProvVoip\Entities\PhonebookEntry::$config[$attribute]['valid']);

        // check if given value is to long
        if (strlen($value) > $maxlen) {
            $validator->setCustomMessages(['phonebook_string' => $attribute.' to long (max. '.$maxlen.' characters allowed)']);

            return false;
        }

        // check all value's characters against the list of valid chars
        $invalids = [];
        foreach (str_split($value) as $char) {
            if (! in_array($char, $valid)) {
                array_push($invalids, $char);
            }
        }
        // input invalid if at least one invalid character has been found
        if (boolval($invalids)) {
            $validator->setCustomMessages(['phonebook_string' => 'The following characters are not allowed in '.$attribute.': '.implode('', array_unique($invalids))]);

            return false;
        }

        // all fine => valid
        return true;
    }

    /**
     * Check if given string (predefined) for phonebookentry is valid.
     * The valid values are defined by Telekom and delivered in several files.
     *
     * @author Patrick Reichel
     */
    public function validatePhonebookPredefinedString($attribute, $value, $parameters)
    {

        // see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
        // when laravel calls the validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
        $validator = \func_get_arg(3);

        // attention: data coming into validators are still htmlencoded from view level!
        $search = html_entity_decode($value);

        // use the method that builds the array for the selects => that contains all valid values…
        if (! array_key_exists($search, \Modules\ProvVoip\Entities\PhonebookEntry::get_options_from_file($attribute))) {
            $validator->setCustomMessages(['phonebook_predefined_string' => $value.' is not a valid value for '.$attribute]);

            return false;
        }

        return true;
    }

    /**
     * Check if given one character option is valid
     *
     * @author Patrick Reichel
     */
    public function validatePhonebookOneCharacterOption($attribute, $value, $parameters)
    {

        // see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
        // when laravel calls the validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
        $validator = \func_get_arg(3);

        // get the allowed chars out of config
        $valid = \Modules\ProvVoip\Entities\PhonebookEntry::$config[$attribute]['in_list'];

        if (! in_array($value, $valid)) {
            $validator->setCustomMessages(['phonebook_one_character_option' => $value.' is not valid for '.$attribute.' (have to be in ['.implode('', $valid).']).']);

            return false;
        }

        return true;
    }

    /**
     * Check values that are entry_type dependend
     *
     * @param $parameters first entry needs to be the entry_type value (add “entry_type” to \Modules\ProvVoip\Entities\PhonebookEntry::rules(); will
     *          then be set in PhonebookEntryController::prepare_rules
     *
     * @author Patrick Reichel
     */
    public function validatePhonebookEntryTypeDependend($attribute, $value, $parameters)
    {

        // see: https://laracasts.com/discuss/channels/general-discussion/extending-validation-with-custom-message-attribute?page=1
        // when laravel calls the validation function (validate) they luckily pass "$this" that is the Validator instance as 4th argument - so we can get it here
        $validator = \func_get_arg(3);

        $entry_type = $parameters[0];

        // define which forms are needed to be filled/empty depending on entry_type
        if (in_array($entry_type, ['P'])) {
            $has_to_be_empty = [
                'company',
            ];
            $has_to_be_set = [
                'salutation',
                'lastname',
            ];
        } elseif (in_array($entry_type, ['B', 'F'])) {
            $has_to_be_empty = [
                'salutation',
                'firstname',
                'lastname',
                'academic_degree',
                'noble_rank',
                'nobiliary_particle',
                'other_name_suffix',
            ];
            $has_to_be_set = [
                'company',
            ];
        }

        // check if data is given for field that has to be empty
        if (in_array($attribute, $has_to_be_empty) && $value) {
            $validator->setCustomMessages(['phonebook_entry_type_dependend' => "The $attribute field has to be empty when entry type is $entry_type."]);

            return false;
        }

        // check if needed field is empty
        if (in_array($attribute, $has_to_be_set) && ! $value) {
            $validator->setCustomMessages(['phonebook_entry_type_dependend' => "The $attribute field is required when entry type is $entry_type."]);

            return false;
        }

        // all checks passed
        return true;
    }

    /**
     * Checks if a BIC entry exists in a config/data file
     *
     * @author Nino Ryschawy
     */
    public function validateBicAvailable($attribute, $value, $parameters)
    {
        $iban = new IBAN(strtoupper($parameters[0]));
        $country = strtolower($iban->Country());
        $bank = $iban->Bank();

        $data = Storage::get('config/billingbase/bic_'.$country.'.csv');

        if (strpos($data, $bank) !== false) {
            return true;
        }

        return false;
    }

    public function validateEmpty($attribute, $value, $parameters)
    {
        // d($value, $attribute, $parameters, $this->getValue($parameters[0]));

        return $value ? false : true;
    }

    /**
     * Checks if given string is IPv4 address
     * Only used as helper – use laravel rule “ipv4” instead
     *
     * @author Patrick Reichel
     */
    protected function validateIPv4Address($attribute, $value, $parameters)
    {
        return boolval(filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
    }

    /**
     * Checks if given string is IPv6 address
     * Only used as helper – use laravel rule “ipv6” instead
     *
     * @author Patrick Reichel
     */
    protected function validateIPv6Address($attribute, $value, $parameters)
    {
        return boolval(filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));
    }

    /**
     * Checks if given string is either IPv4 or IPv6 address
     * Only used as helper – use laravel rule “ip” instead
     *
     * @author Patrick Reichel
     */
    protected function validateIP4Or6Address($attribute, $value, $parameters)
    {
        return
            $this->validateIPv4Address($attribute, $value, $parameters) ||
            $this->validateIPv6Address($attribute, $value, $parameters);
    }

    /**
     * Checks if given string is hostname OR IP address
     *
     * @author Patrick Reichel
     */
    public function validateHostnameOrIp($attribute, $value, $parameters)
    {
        return
            $this->validateHostname($attribute, $value, $parameters) ||
            $this->validateIPv4Address($attribute, $value, $parameters) ||
            $this->validateIPv6Address($attribute, $value, $parameters);
    }

    /**
     * Checks if given string is a hostname
     *
     * @author Patrick Reichel
     */
    protected function validateHostname($attribute, $value, $parameters)
    {
        // check if at least on letter is contained to filter mistyped IP addresses (192.168.10.1111) – such hostnames should not be in use…
        if ((substr_count($value, '.') == 3) && (! preg_match('/[A-Za-z]/', $value))) {
            return false;
        }

        return boolval(filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
    }

    /**
     * Checks if given string is hostname OR IP address
     *
     * @author Patrick Reichel
     */
    public function validateCommaSeparatedHostnamesOrIps($attribute, $value, $parameters)
    {
        $parts = explode(',', $value);
        foreach ($parts as $part) {
            if (! $this->validateHostnameOrIp($attribute, trim($part), $parameters)) {
                return false;
            }
        }

        return true;
    }
}
