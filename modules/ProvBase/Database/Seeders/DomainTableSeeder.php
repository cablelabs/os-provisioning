<?php

namespace Modules\ProvBase\Database\Seeders;

use Modules\ProvBase\Entities\Domain;

class DomainTableSeeder extends \BaseSeeder
{
    public function run()
    {
        foreach (range(1, self::$max_seed) as $index) {
            Domain::create(static::get_fake_data('seed'));
        }
    }

    /**
     * Returns an array with faked domain data; used e.g. in seeding and testing
     *
     * @param $topic Context the method is used in (seed|test)
     *
     * @author Patrick Reichel
     */
    public static function get_fake_data($topic, $contract = null)
    {
        $faker = &\NmsFaker::getInstance();

        $alias = $faker->domainWord();
        foreach (range(1, rand(1, 10)) as $idx) {
            $alias .= ':'.$faker->domainWord();
        }

        $type = Domain::getPossibleEnumValues('type');

        $ret = [
            'name' => $faker->domainName(),
            'alias' => $alias,
            'type' => $type[array_rand($type)],
        ];

        return $ret;
    }
}
