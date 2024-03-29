<?php declare(strict_types=1);

namespace Linker\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;

/**
 * Class SystemdLogHandler
 *
 * @package SystemdLogHandler
 */
class SystemdLogHandler extends AbstractProcessingHandler
{
    const EMERGENCY = 0;
    const ALERT     = 1;
    const CRITICAL  = 2;
    const ERROR     = 3;
    const WARNING   = 4;
    const NOTICE    = 5;
    const INFO      = 6;
    const DEBUG     = 7;

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

    /**
     * @var string the name of the system for the log message
     */
    protected $systemName;

    public function __construct($systemName = null, $level = self::DEBUG, bool $bubble = true)
    {
        $this->systemName = $systemName ?: gethostname();

        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        if (!function_exists('sd_journal_send')) {
            return;    
        }
        
        $context  = $record['context'];
        $severity = array_search($record['level_name'], self::$levels);
        $params   = [
            'MESSAGE=' . $record['message'],
            'PRIORITY=' . $severity,
            'SYSLOG_IDENTIFIER=' . $record['channel'],
            'SYSTEM_NAME=' . $this->systemName
        ];

        if (isset($context['exception']) && $context['exception'] instanceof \Exception) {
            $e        = $context['exception'];
            $params[] = 'CODE_FILE=' . $e->getFile();
            $params[] = 'CODE_LINE=' . $e->getLine();

            if ($record['message'] !== $e->getMessage()) {
                $params[0] = sprintf('MESSAGE=%s: %s', $record['message'], $e->getMessage());
            }
        }

        call_user_func_array('sd_journal_send', $params);
    }
}

