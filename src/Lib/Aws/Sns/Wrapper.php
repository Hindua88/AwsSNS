<?php
namespace Lib\Aws\Sns;

/**
 * AWS SNS API Wrapper
 *
 * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html
 */
class Wrapper
{
    /**
     * listPlatformApplications
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listPlatformApplications
     */
    public function listPlatformApplications()
    {
        $Api = new Api('listPlatformApplications');

        return $Api->execute();
    }

    /**
     * listEndpointsByPlatformApplication
     *
     * @link
     * http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listEndpointsByPlatformApplication
     */
    public function listEndpointsByPlatformApplication($platformApplicationArn = '')
    {
        $Api = new Api('listEndpointsByPlatformApplication');
        $Api->addParam([
            'PlatformApplicationArn' => $platformApplicationArn,
        ]);

        return $Api->execute();
    }

    /**
     * listEndpoints
     * Get all endpoints in all platform applications
     */
    public function listEndpoints()
    {
        $listPlatformApplications = $this->listPlatformApplications();

        $Api = new Api('listEndpointsByPlatformApplication');

        foreach ($listPlatformApplications as $platformApplication) {
            $Api->addParam([
                'PlatformApplicationArn' => $platformApplication['PlatformApplicationArn'],
            ]);
        }

        $listEndpointsByPlatformApplication = $Api->execute();

        $results = $listPlatformApplications;
        foreach ($listEndpointsByPlatformApplication as $key => $endpoints) {
            $results[$key]['Endpoints'] = $endpoints;
        }

        return $results;
    }

    /**
     * getPlatformApplicationAttributes
     *
     * @link
     * http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_getPlatformApplicationAttributes
     */
    public function getPlatformApplicationAttributes($platformApplicationArn = '')
    {
        $Api = new Api('getPlatformApplicationAttributes');
        $Api->addParam([
            'PlatformApplicationArn' => $platformApplicationArn,
        ]);

        return $Api->execute();
    }

    /**
     * createPlatformEndpoint
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_createPlatformEndpoint
     */
    public function createPlatformEndpoint($platform = '', $token = array())
    {
        $Api = new Api('createPlatformEndpoint');
        $platformApplicationArn = Configure::getPlatformApplicationArn($platform);

        if (is_array($token)) {
            foreach ($token as $_token) {
                $Api->addParam([
                    'PlatformApplicationArn' => $platformApplicationArn,
                    'Token' => $_token

                ]);
            }
        } else {
            $Api->addParam([
                'PlatformApplicationArn' => $platformApplicationArn,
                'Token' => $token
            ]);
        }

        return $Api->execute();
    }

    /**
     * getEndpointAttributes
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_getEndpointAttributes
     */
    public function getEndpointAttributes($endpointArn = '')
    {
        $Api = new Api('getEndpointAttributes');
        $Api->addParam([
            'EndpointArn' => $endpointArn,
        ]);

        return $Api->execute();
    }

    /**
     * set Endpoint Enabled
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_setEndpointAttributes
     */
    public function setEnabled($endpointArn = '')
    {
        $Api = new Api('setEndpointAttributes');
        $Api->addParam([
            'EndpointArn' => $endpointArn,
            'Attributes' => [
                'Enabled' => 'True'
            ]
        ]);

        return $Api->execute();
    }

    /**
     * deleteEndpoint
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_deleteEndpoint
     */
    public function deleteEndpoint($endpointArn = '')
    {
        $Api = new Api('deleteEndpoint');
        $Api->addParam([
            'EndpointArn' => $endpointArn,
        ]);

        return $Api->execute();
    }

    /**
     * createTopic
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_createTopic
     */
    public function createTopic($topicName = '')
    {
        if (!mb_check_encoding($topicName, 'ASCII') || preg_match('/[^a-zA-Z_\-0-9]/i',
                $topicName) || strlen($topicName) < 1 || strlen($topicName) > 256
        ) {
            throw new \Exception('Invalid topic name.');
        }

        $Api = new Api('createTopic');
        $Api->addParam([
            'Name' => $topicName,
        ]);

        return $Api->execute();
    }

    /**
     * List Topics
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listTopics
     */
    public function listTopics()
    {
        $Api = new Api('listTopics');

        return $Api->execute();
    }

    /**
     * subscribe
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_subscribe
     */
    public function subscribe($topicArn = '', $endpointArn = '', $protocol = 'application')
    {
        $Api = new Api('subscribe');
        if (is_array($endpointArn)) {
            foreach ($endpointArn as $_endpointArn) {
                $Api->addParam([
                    'TopicArn' => $topicArn,
                    'Endpoint' => $_endpointArn,
                    'Protocol' => $protocol
                ]);
            }
        } else {
            $Api->addParam([
                'TopicArn' => $topicArn,
                'Endpoint' => $endpointArn,
                'Protocol' => $protocol
            ]);
        }

        return $Api->execute();
    }

    /**
     * listSubscriptionsByTopic
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listSubscriptionsByTopic
     */
    public function listSubscriptionsByTopic($topicArn = '')
    {
        $Api = new Api('listSubscriptionsByTopic');
        $Api->addParam([
            'TopicArn' => $topicArn
        ]);

        return $Api->execute();
    }

    /**
     * Publish to Topic
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_publish
     */
    public function publishToTopic($topicArn = '', $message = '')
    {
        $Message = new \Lib\Aws\Sns\Message();
        $message = $Message->getMessage($message);

        $Api = new Api('publish');
        $Api->addParam([
            'TopicArn' => $topicArn,
            'MessageStructure' => 'json',
            'Message' => $message
        ]);

        return $Api->execute();
    }

    /**
     * Publish to Endpoint
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_publish
     */
    public function publishToEndpoint($endpointArn = '', $message = '')
    {
        $Api = new Api('publish');
        $Message = new \Lib\Aws\Sns\Message();

        if (is_array($endpointArn)) {
            foreach ($endpointArn as $_endpointArn) {
                $Api->addParam([
                    'TargetArn' => $_endpointArn,
                    'MessageStructure' => 'json',
                    'Message' => $Message->getArnMessage($_endpointArn, $message)
                ]);
            }
        } else {
            $Api->addParam([
                'TargetArn' => $endpointArn,
                'MessageStructure' => 'json',
                'Message' => $Message->getArnMessage($endpointArn, $message)
            ]);
        }

        return $Api->execute();
    }
}
