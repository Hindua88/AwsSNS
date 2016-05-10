<?php
namespace Lib\Aws\Sns;
use Guzzle\Service\Exception\CommandTransferException;

class Api
{
    protected $Client;
    protected $api;
    protected $modelName;
    protected $params;    

    protected static $apiModelName = [
        'listPlatformApplications'           => 'PlatformApplications',
        'listEndpointsByPlatformApplication' => 'Endpoints',
        'createPlatformEndpoint'             => 'EndpointArn',
        'getEndpointAttributes'              => 'Attributes',
        'setEndpointAttributes'              => '',
        'deleteEndpoint'                     => 'EndpointArn',
        'createTopic'                        => 'TopicArn',
        'listTopics'                         => 'Topics',
        'subscribe'                          => 'SubscriptionArn',
        'getPlatformApplicationAttributes'   => 'Attributes',
        'listSubscriptionsByTopic'           => 'Subscriptions',
        'publish'                            => 'MessageId',
    ];

    public function __construct($api, $params = array())
    {
        if (!isset(self::$apiModelName[$api])) {
            throw new \Exception('Call unknown API.');
        }

        $this->api       = $api;
        $this->modelName = self::$apiModelName[$api];
        $this->params    = (array) $params;
        $this->Client    = Client::build();
    }

    public function addParam($param = array())
    {
        array_push($this->params, (array) $param);
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
        
        // Have many parameters bundle
        } else {
            return $this->parallel();
        }
    }

    /**
     * Execute direct API command
     *
     */
    public function direct()
    {
        $params = reset($this->params) ? reset($this->params) : array();

        $results = array();

        try
        {
            $result = $this->Client->{$this->api}($params);

            if (isset($result[$this->modelName])) {
                $results = $result[$this->modelName];
            } else if ($result instanceof \Guzzle\Service\Resource\Model) {
                $results = 'Success.';
            } else {
                $results = 'Unknown error.';
            }
        }
        catch (\Exception $e)
        {
            $results = $e->getMessage();
        }

        return $results;
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
                $this->Client->getCommand(
                    $this->api,
                    $param
                )
            );
        }

        $results = array();

        try
        {
            $succeedCommands = $this->Client->execute($commands);

            foreach ($succeedCommands as $command) {
                $index = array_search($command, $commands);
                $result = $command->getResult();

                if (isset($result[$this->modelName])) {
                    $results[$index] = $result[$this->modelName];
                } else if ($result instanceof \Guzzle\Service\Resource\Model) {
                    $results[$index] = 'Success.';
                } else {
                    $results[$index] = 'Unknown error.';
                }
            }
        } 
        catch (CommandTransferException $e)
        {
            $succeedCommands = $e->getSuccessfulCommands();
            foreach ($succeedCommands as $command) {
                $index = array_search($command, $commands);
                $result = $command->getResult();

                if (isset($result[$this->modelName])) {
                    $results[$index] = $result[$this->modelName];
                } else if ($result instanceof \Guzzle\Service\Resource\Model) {
                    $results[$index] = 'Success.';
                } else {
                    $results[$index] = 'Unknown error.';
                }
            }


            foreach ($e->getFailedCommands() as $command) {
                $index = array_search($command, $commands);
                $results[$index] = $e->getExceptionForFailedCommand($command)->getMessage();
            }
        }

        return $results;
    }
}
