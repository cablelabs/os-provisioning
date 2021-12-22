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

namespace Tests;

class HelpersTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data provider for testing ValueInThresholdStringData()
     */
    public function provideTestValueInThresholdStringData()
    {
        return [
            [null, '-2..2', false],

            [-1, '..1', true],
            [0, '..1', true],
            [1, '..1', true],
            [1.000001, '..1', false],
            [100000000000000000000000000000, '..1', false],

            [-1, '1..', false],
            [0, '1..', false],
            [1, '1..', true],

            [0, '-2..2', true],
            ['0', '-2..2', true],
            [-2, '-2..2', true],
            ['-2', '-2..2', true],
            [2, '-2..2', true],
            [2.01, '-2..2', false],
            [-2.01, '-2..2', false],

            [0, '-4..-2;2..4', false],
            [4.000000000001, '-4..-2;2..4', false],
            [1.999999999999999, '-4..-2;2..4', false],
            [5.2, '-4..-2;2..4', false],
            [5, '-4..-2;2..4', false],
            [-5, '-4..-2;2..4', false],
            [-5.0, '-4..-2;2..4', false],
            [3.1, '-4..-2;2..4', true],
            [4.0, '-4..-2;2..4', true],
            [3.999999999999999, '-4..-2;2..4', true],
            [3, '-4..-2;2..4', true],
            [-3, '-4..-2;2..4', true],

            [100, '-30..-20;-10..10;20..30', false],
            [-25, '-30..-20;-10..10;20..30', true],
            [0, '-30..-20;-10..10;20..30', true],
            [25, '-30..-20;-10..10;20..30', true],

            [0.1, '-1.5..1.5', true],
            [0.0, '-1.5..1.5', true],
            [2.0, '-1.5..1.5', false],
            [0.1, '-1.5..1', true],
        ];
    }

    /**
     * Data provider for testing ValueInThresholdStringData()
     */
    public function provideTestValuesFromThresholdStringData()
    {
        return [
            [
                '..2',
                [[-PHP_FLOAT_MAX, 2.0]],
            ],
            [
                '2..',
                [[2.0, PHP_FLOAT_MAX]],
            ],
            [
                '-2..2',
                [[-2.0, 2.0]],
            ],
            [
                '-4..-2;2..4',
                [[-4.0, -2.0], [2.0, 4.0]],
            ],
        ];
    }

    /**
     * @dataProvider provideTestValueInThresholdStringData
     */
    public function testValueInThresholdString($value, $thresholds, $expectedResult)
    {
        self::assertSame(
            valueInThresholdString($value, $thresholds),
            $expectedResult,
        );
    }

    public function testValueInThresholdStringStringGiven()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessageMatches('/is not numeric$/');
        valueInThresholdString('foobar', '..1');
    }

    /**
     * @dataProvider provideTestValuesFromThresholdStringData
     */
    public function testValuesFromThresholdString($thresholds, $expectedResult)
    {
        self::assertSame(
            valuesFromThresholdString($thresholds),
            $expectedResult,
        );
    }
}
