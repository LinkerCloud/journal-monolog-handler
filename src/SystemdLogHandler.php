<?php

namespace SystemdLogHandler;

use phpDocumentor\Reflection\Types\Object_;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Class SystemdLogHandler
 * @package SystemdLogHandler
 */
class SystemdLogHandler extends AbstractProcessingHandler
{
    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;

    protected static $levels = array(
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    );


    public function __construct($level = self::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $context = $record['context'];
        $severity = array_search($record['level_name'], self::$levels);
        $params = [
            'MESSAGE='. $record['formatted'],
            'PRIORITY=' . $severity,
            'SYSLOG_IDENTIFIER=' . $record['channel']
        ];

        if (isset($context['exception']) && $context['exception'] instanceof \Exception) {
            $e = $context['exception'];
            $params[] = 'CODE_FILE=' . $e->getFile();
            $params[] = 'CODE_LINE=' . $e->getLine();
        }

        call_user_func_array('sd_journal_send', $params);
    }
}

