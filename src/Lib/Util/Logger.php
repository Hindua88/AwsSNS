<?php
namespace Lib\Util;

/* Usage:
    $log = new Logger("/tmp/log.txt", Logger::DEBUG);
    $log->debug('debug log'); // Output: 2016-05-13 05:29:37 - Debug --> debug log 
    $log->info('infor log'); // Output: 2016-05-13 05:29:37 - Info --> infor log 
    $log->warn('warn log'); // Output: 2016-05-13 05:29:37 - Warn --> warn log 
    $log->error('error log'); // Output: 2016-05-13 05:29:37 - Error --> error log 
    $log->fatal('fatal log'); // Output: 2016-05-13 05:29:37 - Fatal --> fatal log 
*/
class Logger
{
    const DEBUG = 1; // Most Verbose
    const INFO = 2;
    const WARN = 3;
    const ERROR = 4;
    const FATAL = 5; // Least Verbose
    const OFF = 6; // Nothing at all.

    const LOG_OPEN = 1;
    const OPEN_FAILED = 2;
    const LOG_CLOSED = 3;

    public $log_status = Logger::LOG_CLOSED;
    public $dateformat = "Y-m-d H:i:s";
    public $message_queue;
    private $log_file;
    private $priority = Logger::INFO;
    private $file_handle;

    public function __construct($filepath, $priority)
    {
        if ($priority == Logger::OFF) {
            return;
        }

        $this->log_file = $filepath;
        $this->message_queue = array();
        $this->priority = $priority;

        if (file_exists($this->log_file)) {
            if (!is_writable($this->log_file)) {
                $this->log_status = Logger::OPEN_FAILED;
                $this->message_queue[] = "The file exists, but could not be opened for writing. Check that appropriate permissions have been set.";
                return;
            }
        }

        if ($this->file_handle = fopen($this->log_file, "a")) {
            $this->log_status = Logger::LOG_OPEN;
            $this->message_queue[] = "The log file was opened successfully.";
        } else {
            $this->log_status = Logger::OPEN_FAILED;
            $this->message_queue[] = "The file could not be opened. Check permissions.";
        }

        return;
    }

    public function __destruct()
    {
        if ($this->file_handle) {
            fclose($this->file_handle);
        }
    }

    public function info($line)
    {
        $this->log($line, Logger::INFO);
    }

    public function debug($line)
    {
        $this->log($line, Logger::DEBUG);
    }

    public function warn($line)
    {
        $this->log($line, Logger::WARN);
    }

    public function error($line)
    {
        $this->log($line, Logger::ERROR);
    }

    public function fatal($line)
    {
        $this->log($line, Logger::FATAL);
    }

    public function log($line, $priority)
    {
        if ($this->priority <= $priority) {
            $status = $this->getTimeLine($priority);
            $this->writeFreeFormLine("$status $line \n");
        }
    }

    public function writeFreeFormLine($line)
    {
        if ($this->log_status == Logger::LOG_OPEN && $this->priority != Logger::OFF) {
            if (fwrite($this->file_handle, $line) === false) {
                $this->message_queue[] = "The file could not be written to. Check that appropriate permissions have been set.";
            }
        }
    }

    private function getTimeLine($level)
    {
        $time = date($this->dateformat);
        switch ($level) {
            case Logger::INFO:
                return "$time - Info -->";
            case Logger::WARN:
                return "$time - Warn -->";
            case Logger::DEBUG:
                return "$time - Debug -->";
            case Logger::ERROR:
                return "$time - Error -->";
            case Logger::FATAL:
                return "$time - Fatal -->";
            default:
                return "$time - Log -->";
        }
    }
}

