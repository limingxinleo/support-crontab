<?php

declare(strict_types=1);
/**
 * This file is part of 李铭昕.
 *
 * @contact  limingxin@swoft.org
 */

namespace Test\Cases;

use limx\Support\Crontab;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends AbstractTestCase
{
    public function testCrontab()
    {
        $time = 1557564660;
        $this->assertTrue(Crontab::current('* * * * *', $time));
        $this->assertFalse(Crontab::current('1 * * * *', $time));
        $this->assertTrue(Crontab::current('51 * * * *', $time));
        $this->assertSame(1557564720, Crontab::next('52 * * * *', $time));
    }
}
