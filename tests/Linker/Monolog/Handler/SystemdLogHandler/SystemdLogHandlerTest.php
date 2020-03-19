<?php

namespace Linker\Monolog\Handler;

use PHPUnit\Framework\TestCase;

class SystemdLogHandlerTest extends TestCase
{
    /**
     * @covers Linker\Monolog\Handler\SystemdLogHandler::__construct
     */
    public function testConstruct()
    {
        $handler = new SystemdLogHandler();
        $this->assertInstanceOf(SystemdLogHandler::class, $handler);

        $handler = new SystemdLogHandler(LOG_PERROR);
        $this->assertInstanceOf(SystemdLogHandler::class, $handler);
    }

}
