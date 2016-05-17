<?php
namespace Lib\Aws\Sns;

require_once dirname(dirname(__DIR__)) . DS . 'Util' . DS . 'autoload.php';

use Guzzle\Service\Exception\CommandTransferException;
use Lib\Util\Constants as Constants;
use Lib\Util\Logger as Logger;


class Api
{
    protected $client; // SnsClient
    protected $api;
    protected $model_name;
    protected $params;
    private $log;

    protected static $list_model_name = [
        'listPlatformApplications' => 'PlatformApplications',
        'listEndpointsByPlatformApplication' => array('Endpoints', 'NextToken'),
        'createPlatformEndpoint' => 'EndpointArn',
        'getEndpointAttributes' => 'Attributes',
        'setEndpointAttributes' => '',
        'deleteEndpoint' => 'EndpointArn',
        'createTopic' => 'TopicArn',
        'deleteTopic' => '',
        'listTopics' => array('Topics', 'NextToken'),
        'subscribe' => 'SubscriptionArn',
        'unsubscribe' => '',
        'getPlatformApplicationAttributes' => 'Attributes',
        'listSubscriptionsByTopic' => array('Subscriptions', 'NextToken'),
        'publish' => 'MessageId',
    ];

    /*
     * Constuct api
     *
     * @param string $api
     *
     * @return null
     */
    public function __construct($api, $params = array())
    {
        if (!isset(self::$list_model_name[$api])) {
            throw new \Exception('Call unknown API.');
        }

        $this->api = $api;
        $this->model_name = self::$list_model_name[$api];
        $this->params = (array)$params;
        if (Constants::ENABLE_DEBUG_LOG && ! empty(Constants::FILE_DEBUG_LOG)) {
            $this->log = new Logger(Constants::FILE_DEBUG_LOG, Constants::LOG_LEVEL); 
        }
    }

    /*
     * Get SnsClient propery
     *
     * @param object SnsClient
     *
     * @return null
     */
    public function setSnsClient($sns_client)
    {
        $this->client = $sns_client;
    }

    /*
     * Add list of param for api
     *
     * @param array $params
     *
     * @return null
     */
    public function addParam($params = array())
    {
        array_push($this->params, (array)$params);
    }

    /**
     * Execute API command
     */
    public function execute()
    {
        // Don't have parameters or Just have only one parameters bundle 
        if (empty($this->params) || count($this->params) == 1) {
            return $this->direct();
        } else { // Have many parameters bundle
            $total_param = count($this->params);
            if (Constants::LIMIT_MAX_PARAM_API > 0 &&
                $total_param > Constants::LIMIT_MAX_PARAM_API) {
                throw new \Exception("Api: {$this->api} total max params {$total_param} > limit max param " . Constants::LIMIT_MAX_PARAM_API);
            }
            return $this->parallel();
        }
    }

    /*
     * Parse data response
     *
     * @paran array $data
     *
     * @return array
     */
    private function parseResultData($data, &$result)
    {
        if (is_array($this->model_name)) {
            foreach ($this->model_name as $key) {
                if (isset($data[$key])) {
                    $result[$key] = $data[$key];
                }
            }
        } elseif (isset($data[$this->model_name])) {
            $result = $data[$this->model_name];
        } else {
            if ($data instanceof \Guzzle\Service\Resource\Model) {
                $result = 'Success.';
            } else {
                $result = 'Unknown error.';
            }
        }
    }

    /**
     * Execute direct API command
     */
    private function direct()
    {
        // Set the internal pointer of an array to its first element
        $params = reset($this->params) ? reset($this->params) : array();
        $result = array();
        try {
            $data = $this->client->{$this->api}($params);
            $this->parseResultData($data, $result);
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    /**
     * Execute API in parallel
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/guide/latest/feature-commands.html
     */
    private function parallel()
    {
        $commands = array();
        foreach ($this->params as $param) {
            array_push(
                $commands,
                $this->client->getCommand(
                    $this->api,
                    $param
                )
            );
        }

        $result = array();
        try {
            $succeedCommands = $this->client->execute($commands);
            foreach ($succeedCommands as $command) {
                $index = array_search($command, $commands);
                $data = $command->getResult();
                $this->parseResultData($data, $result[$index]);
            }
        } catch (CommandTransferException $e) {
            // Success
            $succeedCommands = $e->getSuccessfulCommands();
            foreach ($succeedCommands as $command) {
                $index = array_search($command, $commands);
                $data = $command->getResult();
                $this->parseResultData($data, $result[$index]);
            }
            if ($e->getFailedCommands()) {
                $this->writeErrorLog("SnsMobileClient -> Api {$this->api} (parallel): total commands " . count($commands)
                    . ", fail: " . count($e->getFailedCommands()));
            }
            // Fail 
            foreach ($e->getFailedCommands() as $command) {
                $index = array_search($command, $commands);
                $result[$index] = $e->getExceptionForFailedCommand($command)->getMessage();
                $this->writeErrorLog("SnsMobileClient -> Api {$this->api} (parallel) "
                    . $e->getExceptionForFailedCommand($command)->getMessage());
            }
        }

        return $result;
    }

    /*
     * Write log
     */
    private function writeErrorLog($message)
    {
        if ($this->log) {
            $this->log->error($message);
        } else {
            error_log($message);
        }
    } 

    public function __destruct()
    {
        if ($this->log) {
            unset($this->log);
        }
    }
}
