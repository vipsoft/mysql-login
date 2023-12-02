<?php
/**
 * @copyright 2023 Anthon Pang
 * @license MIT
 */
namespace VIPSoft\Tests;

use VIPSoft\MySQLLogin;

/**
 * MySQLLogin test
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class MySQLLoginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test reader/decoder
     */
    public function testGet()
    {
        $reader = new MySQLLogin(__DIR__ . '/fixtures/mylogin.cnf');

        $credentials = $reader->get('client');

        $this->assertEquals('root', $credentials['user']);
        $this->assertEquals('p455w0rd', $credentials['password']);
        $this->assertEquals('localhost', $credentials['host']);
    }
}
