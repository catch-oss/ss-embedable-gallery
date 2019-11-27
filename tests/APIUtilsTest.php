<?php

namespace CatchDesign\SSAPInterfaceTest;

use SapphireTest;

use CatchDesign\SSAPInterface\APIUtils;



class APIUtilsTest extends SapphireTest {

    public function testCIDRMatch() {
        $tests = [
            (object) [
                'CIDR' => '10.0.0.1/32',
                'pass' => [
                    '10.0.0.1'
                ],
                'fail' => [
                    '10.0.0.2'
                ]
            ],
            (object) [
                'CIDR' => '10.0.0.0/16',
                'pass' => [
                    '10.0.0.0',
                    '10.0.255.255',
                ],
                'fail' => [
                    '10.1.0.1'
                ]
            ],
            (object) [
                'CIDR' => '0.0.0.0/1',
                'pass' => [
                    '0.0.0.0',
                    '127.255.255.255',
                ],
                'fail' => [
                    '129.0.0.1'
                ]
            ],
            (object) [
                'CIDR' => '0.0.0.0/0',
                'pass' => [
                    '0.0.0.0',
                    '255.255.255.255',
                ],
                'fail' => []
            ],
        ];

        foreach ($tests as $test) {

            foreach ($test->pass as $tPass) {
                $this->assertEquals(true, APIUtils::cidr_match($tPass, $test->CIDR));
            }

            foreach ($test->fail as $tFail) {
                $this->assertEquals(false, APIUtils::cidr_match($tFail, $test->CIDR));
            }
        }
    }
}
