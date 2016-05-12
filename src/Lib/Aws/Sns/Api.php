<?php
namespace Lib\Aws\Sns;

use Guzzle\Service\Exception\CommandTransferException;

class Api
{
    protected $client; // SnsClient
    protected $api;
    protected $model_name;
    protected $params;

    protected static $list_model_name = [
        'listPlatformApplications' => 'PlatformApplications',
        'listEndpointsByPlatformApplication' => array('Endpoints', 'NextToken'),
        'createPlatformEndpoint' => 'EndpointArn',
        'getEndpointAttributes' => 'Attributes',
        'setEndpointAttributes' => '',
        'deleteEndpoint' => 'EndpointArn',
        'createTopic' => 'TopicArn',
        'listTopics' => array('Topics', 'NextToken'),
        'subscribe' => 'SubscriptionArn',
        'getPlatformApplicationAttributes' => 'Attributes',
        'listSubscriptionsByTopic' => array('Subscriptions', 'NextToken'),
        'publish' => 'MessageId',
    ];

    public function __construct($api, $params = array())
    {
        if (!isset(self::$list_model_name[$api])) {
            throw new \Exception('Call unknown API.');
        }

        $this->api = $api;
        $this->model_name = self::$list_model_name[$api];
        $this->params = (array)$params;
    }

    /*
     * Get SnsClient propery
     */
    public function setSnsclient($sns_client)
    {
        $this->client = $sns_client;
    }

    public function addParam($param = array())
    {
        array_push($this->params, (array)$param);
    }

    /**
     * Execute API command
     *
     */
    public function execute()
    {
        // Don't have parameters or Just have only one parameters bundle 
        if (empty($this->params) || count($this->params) == 1) {
            return $this->direct();
        } else { // Have many parameters bundle
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
     *
     */
    public function direct()
    {
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
    public function parallel()
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
                error_log("SnsMoibleClient -> Api {$this->api} (parallel): total commands " . count($commands)
                    . ", fail: " . count($e->getFailedCommands()));
            }
            // Fail 
            foreach ($e->getFailedCommands() as $command) {
                $index = array_search($command, $commands);
                $result[$index] = $e->getExceptionForFailedCommand($command)->getMessage();
                error_log("SnsMoibleClient -> Api {$this->api} (parallel) "
                    . $e->getExceptionForFailedCommand($command)->getMessage());
            }
        }

        return $result;
    }
}
