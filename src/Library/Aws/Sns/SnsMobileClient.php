<?php
namespace Lib\Aws\Sns;

/**
 * AWS SNS API Wrapper: SnsMobileClient
 * SnsMobileClient only support ios and android
 *
 * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html
 */

# Include file autoload.php of aws-sdk-php library
require_once ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'; // To do: Replace ROOT directory of your project to load aws-sdk-php lib

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Util' . DIRECTORY_SEPARATOR . 'autoload.php';

use Aws\Sns\SnsClient as SnsClient;
use Guzzle\Plugin\Log\LogPlugin as LogPlugin;
use Lib\Util\CommonHelper as CommonHelper;
use Lib\Util\Constants as Constants;
use Lib\Util\Logger as Logger;

class SnsMobileClient
{
    private $sns_key;
    private $sns_secret;
    private $sns_region;
    private $platform_application;
    private $platform; // is APNS, APNS_SANDBOX or GCM
    private $sns_client;
    private $file_request_log;
    private $log;

    /*
     * Construct SnsMobileClient
     * 
     * @param string $sns_key
     * @param string $sns_secret
     * @param string $sns_region
     * @param string $platform_application
     *
     * @return null
     */
    function __construct($sns_key, $sns_secret, $sns_region, $platform_application)
    {
        if (empty($sns_key) || empty($sns_secret) || empty($sns_region) || empty($platform_application)) {
            throw new \Exception("Invalid params!");
        }
        $this->sns_key = $sns_key;
        $this->sns_secret = $sns_secret;
        $this->sns_region = $sns_region;
        $this->platform_application = $platform_application;
        $arn = new Arn($this->platform_application);
        $this->platform = $arn->platform;
        if (empty($this->platform)) {
            throw new \Exception("Wrong platform_application: {$platform_application} => not detect platform!");
        }
        $platforms = array(Platform::APNS, Platform::APNS_SANDBOX, Platform::GCM);
        if (! in_array($this->platform, $platforms)) {
            throw new \Exception("Platform {$this->platform} is not supported!");
        }

        if (Constants::ALLOW_CHECK_IP_PRODUCTION) {
            // Check correct enviroment to push notification for ios
            $environment = CommonHelper::getEnvironmentByIPAddress(); 
            if ($environment == Constants::ENV_DEVELOP && $this->platform == Platform::APNS) {
                throw new \Exception("Environment " . Constants::ENV_DEVELOP . " could not use config product. Please check config aws sns!");  
            }
        }

        // Init SnsClient
        $this->sns_client = SnsClient::factory(array(
            'key' => $sns_key,
            'secret' => $sns_secret,
            'region' => $sns_region,
        ));

        // Set log file
        if (Constants::ENABLE_REQUEST_LOG && ! empty(Constants::FILE_REQUEST_LOG)) {
            $this->setDebugRequestFileLog(Constants::FILE_REQUEST_LOG); // View request, response api
        }
        if (Constants::ENABLE_DEBUG_LOG && ! empty(Constants::FILE_DEBUG_LOG)) {
            $this->setDebugFileLog(Constants::FILE_DEBUG_LOG); // Custom log
        }
    }

    /*
     * Get name topic by environment (production or sandbox) with ios and android
     *
     * @param string $name
     *
     * @return string
     */
    public function getTopicName($name)
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " to get topic name with name {$name}");
        if (empty($name)) {
            return '';
        }
        $result = Constants::PREFIX_TOPIC_NAME_AWS_SNS;
        if ($this->platform == Platform::APNS_SANDBOX) {
            $result .= '_' . Constants::OS_IOS . '_' . Constants::ENV_DEVELOP_PUSH . '_' . $name;
        } elseif ($this->platform == Platform::APNS) {
            $result .= '_' . Constants::OS_IOS . '_' . Constants::ENV_PRODUCT_PUSH . '_' . $name;
        } elseif (Constants::ENV == Constants::ENV_PRODUCT) {
            $result .= '_' . Constants::OS_ANDROID . '_' . Constants::ENV_PRODUCT_PUSH . '_' . $name;
        } else {
            $result .= '_' . Constants::OS_ANDROID . '_' . Constants::ENV_DEVELOP_PUSH . '_' . $name;
        }
        $result = strtoupper($result);

        return $result;
    }
    
     /*
     * Get Topic Atrributes
     *
     * @param string $topicArn
     *
     * @return array
     *
Ex: array(8) {
  ["Policy"]=>
  string(484) "{"Version":"2008-10-17","Id":"__default_policy_ID","Statement":[{"Sid":"__default_statement_ID","Effect":"Allow","Principal":{"AWS":"*"},"Action":["SNS:GetTopicAttributes","SNS:SetTopicAttributes","SNS:AddPermission","SNS:RemovePermission","SNS:DeleteTopic","SNS:Subscribe","SNS:ListSubscriptionsByTopic","SNS:Publish","SNS:Receive"],"Resource":"arn:aws:sns:ap-southeast-1:634501430222:MY168DZO_IOS_SANDBOX_DEMO_TEST","Condition":{"StringEquals":{"AWS:SourceOwner":"634501430222"}}}]}"
  ["Owner"]=>
  string(12) "634501430222"
  ["SubscriptionsPending"]=>
  string(1) "0"
  ["TopicArn"]=>
  string(70) "arn:aws:sns:ap-southeast-1:634501430222:MY168DZO_IOS_SANDBOX_DEMO_TEST"
  ["EffectiveDeliveryPolicy"]=>
  string(227) "{"http":{"defaultHealthyRetryPolicy":{"minDelayTarget":20,"maxDelayTarget":20,"numRetries":3,"numMaxDelayRetries":0,"numNoDelayRetries":0,"numMinDelayRetries":0,"backoffFunction":"linear"},"disableSubscriptionOverrides":false}}"
  ["SubscriptionsConfirmed"]=>
  string(3) "941"
  ["DisplayName"]=>
  string(0) ""
  ["SubscriptionsDeleted"]=>
  string(1) "0"
}
     */
    public function getTopicAttributes($topicArn = '')
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " to get attributes topic: {$topicArn}");
        $api = new Api('getTopicAttributes');
        $api->setSnsClient($this->sns_client);
        $api->addParam([
            'TopicArn' => $topicArn,
        ]);

        return $api->execute();
    }

    /*
     * Set debug request log
     *
     * @param string $path_file
     *
     * @return null
     */
    public function setDebugRequestFileLog($path_file)
    {
        $this->file_request_log = $path_file;
        if (file_exists($this->file_request_log) && ! is_writable($this->file_request_log)) {
            throw new \Exception("The file exists, but could not be opened for writing. Check that appropriate permissions have been set.");
        }
        if ($stream = fopen($this->file_request_log, "a")) {
            $this->sns_client->addSubscriber(LogPlugin::getDebugPlugin(true, $stream));
        }
    }

    /*
     * Set debug log by level: (debug, info, warn, error, fatal)
     * @param string $path_file
     *
     * @return null
     */
    public function setDebugFileLog($path_file)
    {
        $this->log = new Logger($path_file, Constants::LOG_LEVEL); 
    }
    
    /*
     * Write debug log
     */
    public function writeDebugLog($message)
    {
        if ($this->log) {
            $this->log->debug($message);
        }
    }

    /*
     * Write info log
     */
    public function writeInfoLog($message)
    {
        if ($this->log) {
            $this->log->info($message);
        }
    }

    /*
     * Write warn log
     */
    public function writeWarnLog($message)
    {
        if ($this->log) {
            $this->log->warn($message);
        }
    }

    /*
     * Write error log
     */
    public function writeErrorLog($message)
    {
        if ($this->log) {
            $this->log->error($message);
        }
    }

    /**
     * listPlatformApplications
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listPlatformApplications
     *
     * @return array
     *
        array (n) { [0]=>
          array(2) {
            ["PlatformApplicationArn"]=>
            string(59) "arn:aws:sns:ap-southeast-1:69696696969:app/APNS/168dzo_pro"
            ["Attributes"]=>
            array(2) {
              ["Enabled"]=>
              string(4) "true"
              ["AppleCertificateExpirationDate"]=>
              string(20) "2017-04-22T03:31:37Z"
            }
          }
          [1]=>
          array(2) {
            ["PlatformApplicationArn"]=>
            string(61) "arn:aws:sns:ap-southeast-1:69696696969:app/APNS/ixu_ios_prod"
            ["Attributes"]=>
            array(2) {
              ["Enabled"]=>
              string(4) "true"
              ["AppleCertificateExpirationDate"]=>
              string(20) "2016-10-26T10:14:01Z"
            }
          }
            ...
        }
     */
    public function listPlatformApplications()
    {
        $this->writeDebugLog("Execute " . __FUNCTION__);
        $api = new Api('listPlatformApplications');
        $api->setSnsClient($this->sns_client);

        return $api->execute();
    }

    /**
     * Lists the endpoints and endpoint attributes for devices in a supported push notification service, such as GCM and APNS. The results for ListEndpointsByPlatformApplication are paginated and return a limited list of endpoints, up to 100. If additional records are available after the first page results, then a NextToken string will be returned. To receive the next page, you call ListEndpointsByPlatformApplication again using the NextToken string received from the previous call. When there are no more records to return, NextToken will be null.
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listEndpointsByPlatformApplication
     *
     * @return array 
        Example: 
        array(9) {
          [0]=>
          array(2) {
            ["PlatformApplicationArn"]=>
            string(59) "arn:aws:sns:ap-southeast-1:69696696969:app/APNS/168dzo_pro"
            ["Attributes"]=>
            array(2) {
              ["Enabled"]=>
              string(4) "true"
              ["AppleCertificateExpirationDate"]=>
              string(20) "2017-04-22T03:31:37Z"
            }
          }
          [1]=>
          array(2) {
            ["PlatformApplicationArn"]=>
            string(61) "arn:aws:sns:ap-southeast-1:69696696969:app/APNS/ixu_ios_prod"
            ["Attributes"]=>
            array(2) {
              ["Enabled"]=>
              string(4) "true"
              ["AppleCertificateExpirationDate"]=>
              string(20) "2016-10-26T10:14:01Z"
            }
          }
        ...
        }
     */
    public function listEndpointsByPlatformApplication($nextToken = null)
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " with nextToken {$nextToken}");
        $api = new Api('listEndpointsByPlatformApplication');
        $api->setSnsClient($this->sns_client);
        
        if ($nextToken) {
            $api->addParam([
                'PlatformApplicationArn' => $this->platform_application,
                'NextToken' => $nextToken,
            ]);
        } else {
            $api->addParam([
                'PlatformApplicationArn' => $this->platform_application,
            ]);
        }

        return $api->execute();
    }

    /**
     * getPlatformApplicationAttributes
     *
     * @link
     * http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_getPlatformApplicationAttributes
     *
     * @return array
Example: array(2) {
  ["Enabled"]=>
  string(4) "true"
  ["AppleCertificateExpirationDate"]=>
  string(20) "2016-08-06T10:18:19Z"
}
     */
    public function getPlatformApplicationAttributes()
    {
        $this->writeDebugLog("Execute " . __FUNCTION__);
        $api = new Api('getPlatformApplicationAttributes');
        $api->setSnsClient($this->sns_client);
        $api->addParam([
            'PlatformApplicationArn' => $this->platform_application,
        ]);

        return $api->execute();
    }

    /**
     * createPlatformEndpoint
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_createPlatformEndpoint
    
     * @return string (endpointArn) or array
Example: array: array(2) {
  [0]=>
  string(107) "arn:aws:sns:ap-southeast-1:634501430222:endpoint/APNS_SANDBOX/My168dzo/5be0ec00-2c41-3a29-8921-9b613a4196cf"
  [1]=>
  string(107) "arn:aws:sns:ap-southeast-1:634501430222:endpoint/APNS_SANDBOX/My168dzo/fa6e559d-e0d8-33c0-a185-ed989c7f76e3"
} 
     */
    public function createPlatformEndpoint($token = array())
    {
        if (is_array($token)) {
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to create multi endpoint with " . count($token) . " tokens");
        } else {
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to create endpoint with token: {$token}");
        }
        $api = new Api('createPlatformEndpoint');
        $api->setSnsClient($this->sns_client);

        if (is_array($token)) {
            foreach ($token as $_token) {
                $api->addParam([
                    'PlatformApplicationArn' => $this->platform_application,
                    'Token' => $_token,
                ]);
            }
        } else {
            $api->addParam([
                'PlatformApplicationArn' => $this->platform_application,
                'Token' => $token,
            ]);
        }

        return $api->execute();
    }

    /**
     * getEndpointAttributes
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_getEndpointAttributes
     *
     * @return array
Ex: array(2) {
  ["Enabled"]=>
  string(4) "true"
  ["Token"]=>
  string(64) "bd595bdbe75d0d588a37c5a545262cfcf070ef3bd82768aa203c94ec3feadbd0"
}
     */
    public function getEndpointAttributes($endpointArn = '')
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " with end point {$endpointArn}");
        $api = new Api('getEndpointAttributes');
        $api->setSnsClient($this->sns_client);
        $api->addParam([
            'EndpointArn' => $endpointArn,
        ]);

        return $api->execute();
    }

    /**
     * set Endpoint Enabled
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_setEndpointAttributes
     */
    public function setEndpointEnabled($endpointArn = '')
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " with end point {$endpointArn}");
        $api = new Api('setEndpointAttributes');
        $api->setSnsClient($this->sns_client);
        $api->addParam([
            'EndpointArn' => $endpointArn,
            'Attributes' => [
                'Enabled' => 'true'
            ]
        ]);

        return $api->execute();
    }

    /**
     * set Endpoint Enabled
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_setEndpointAttributes
     */
    public function setEndpointDisabled($endpointArn = '')
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " with end point {$endpointArn}");
        $api = new Api('setEndpointAttributes');
        $api->setSnsClient($this->sns_client);
        $api->addParam([
            'EndpointArn' => $endpointArn,
            'Attributes' => [
                'Enabled' => 'false'
            ]
        ]);

        return $api->execute();
    }

    /**
     * deleteEndpoint
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_deleteEndpoint
     *
     * @return string | array
Example return array:
array(2) {
  [0]=>
  string(8) "Success."
  [1]=>
  string(8) "Success."
}
     */
    public function deleteEndpoint($endpointArn = '')
    {
        if (is_array($endpointArn)) {
            $total_endpoint = count($endpointArn);
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to delete total {$total_endpoint} end points");
        } else {
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to delete {$endpointArn} end point");
        }
        $api = new Api('deleteEndpoint');
        $api->setSnsClient($this->sns_client);
        if (is_array($endpointArn)) {
            foreach ($endpointArn as $_endpointArn) {
                $api->addParam([
                    'EndpointArn' => $_endpointArn,
                ]);
            }
        } else {
            $api->addParam([
                'EndpointArn' => $endpointArn,
            ]);
        }

        return $api->execute();
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

        $this->writeDebugLog("Execute " . __FUNCTION__ . " to create topic: {$topicName}");
        $api = new Api('createTopic');
        $api->setSnsClient($this->sns_client);
        $api->addParam([
            'Name' => $topicName,
        ]);

        return $api->execute();
    }

    /**
     * deleteTopic
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_deleteTopic
     */
    public function deleteTopic($topicArn = '')
    {
        if (is_array($topicArn)) {
            $total = count($topicArn);
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to delete {$total} topic arn");
        } else {
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to delete topic arn: {$topicArn}");
        }
        $api = new Api('deleteTopic');
        $api->setSnsClient($this->sns_client);
        if (is_array($topicArn)) {
            foreach ($topicArn as $_topicArn) {
                $api->addParam([
                    'TopicArn' => $_topicArn,
                ]);
            }
        } else {
            $api->addParam([
                'TopicArn' => $topicArn,
            ]);
        }

        return $api->execute();
    }

    /**
     * List Topics
     * Returns a list of the requester's topics. Each call returns a limited list of topics, up to 100. If there are more topics, a NextToken is also returned. Use the NextToken parameter in a new ListTopics call to get further results.
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listTopics
     * @return array
Ex:
array(100) {
  [0]=>
  array(1) {
    ["TopicArn"]=>
    string(65) "arn:aws:sns:ap-southeast-1:69696696969:168DZO_IOS_PROD_PUSHALL_1"
  }
  [1]=>
  array(1) {
    ["TopicArn"]=>
    string(57) "arn:aws:sns:ap-southeast-1:69696696969:168dzo_daudm_test"
  }
...
}
     */
    public function listTopics($nextToken = null)
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " to list topic with nextToken {$nextToken}");
        $api = new Api('listTopics');
        $api->setSnsClient($this->sns_client);
        if ($nextToken) {
            $api->addParam([
                'NextToken' => $nextToken,
            ]);
        }

        return $api->execute();
    }

    /**
     * subscribe
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_subscribe
     */
    public function subscribe($topicArn = '', $endpointArn = '', $protocol = 'application')
    {
        $total_endpoint = 0;
        if (is_array($endpointArn)) {
            $total_endpoint = count($endpointArn);
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to add total end point {$total_endpoint} into topic {$topicArn}");
        } else {
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to add end point {$endpointArn} into topic {$topicArn}");
            $api = new Api('subscribe');
            $api->setSnsClient($this->sns_client);
            $api->addParam([
                'TopicArn' => $topicArn,
                'Endpoint' => $endpointArn,
                'Protocol' => $protocol
            ]);

            return $api->execute();
        }
        if ($total_endpoint == 0) {
            return;
        }
        $result = array();
        $page_size = Constants::LIMIT_PARALLEL_API; 
        if ($page_size > 0) {
            $total_page = intval(ceil($total_endpoint / $page_size));  // round up to next highest integer
        } else {
            $total_page = 1;
        }
        for ($page = 1; $page <= $total_page; $page ++) {
            $api = new Api('subscribe');
            $api->setSnsClient($this->sns_client);
            $endpoints = array();
            if ($page_size > 0) {
                $start = ($page - 1) * $page_size; 
                $endpoints = array_slice($endpointArn, $start, $page_size);
            } else {
                $endpoints = $endpointArn;
            }
            foreach ($endpoints as $_endpointArn) {
                $api->addParam([
                    'TopicArn' => $topicArn,
                    'Endpoint' => $_endpointArn,
                    'Protocol' => $protocol
                ]);
            }

            $result = array_merge($result, $api->execute());
        }

        return $result;
    }

    /*
     * unsubscribe
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.Sns.SnsClient.html#_unsubscribe
     */
    public function unsubscribe($subscriptionArn = '')
    {
        if (is_array($subscriptionArn)) {
            $total = count($subscriptionArn);
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to delete total {$total} subscribes");
        } else {
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to delete {$subscriptionArn} subscribe");
        }
        $api = new Api('unsubscribe');
        $api->setSnsClient($this->sns_client);
        if (is_array($subscriptionArn)) {
            foreach ($subscriptionArn as $_subscriptionArn) {
                $api->addParam([
                    'SubscriptionArn' => $_subscriptionArn,
                ]);
            }
        } else {
            $api->addParam([
                'SubscriptionArn' => $subscriptionArn,
            ]);
        }

        return $api->execute();
    }

    /**
     * listSubscriptionsByTopic
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_listSubscriptionsByTopic
     *
     * @param string $topicArn
     * @param string $nextToken
     *
     * @return array ('Subscriptions' => array, 'NextToken' => string);
     */
    public function listSubscriptionsByTopic($topicArn = '', $nextToken = null)
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " to list subscriptions {$topicArn} topic with nextToken {$nextToken}");
        $api = new Api('listSubscriptionsByTopic');
        $api->setSnsClient($this->sns_client);
        if ($nextToken) {
            $api->addParam([
                'TopicArn' => $topicArn,
                'NextToken' => $nextToken,
            ]);
        } else {
            $api->addParam([
                'TopicArn' => $topicArn,
            ]);
        }

        return $api->execute();
    }

    /**
     * Publish to Topic
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_publish
     *
     * @param string $topicArn
     * @param array $content
     *
     * @return string
     */
    public function publishToTopic($topicArn = '', $content = array())
    {
        $this->writeDebugLog("Execute " . __FUNCTION__ . " to publish message to {$topicArn} topic");
        $message = new Message($this->platform, $content);
        $this->writeDebugLog("Message {$message->getData()}");

        $api = new Api('publish');
        $api->setSnsClient($this->sns_client);
        $api->addParam([
            'TopicArn' => $topicArn,
            'MessageStructure' => 'json',
            'Message' => $message->getData(),
        ]);

        return $api->execute();
    }

    /**
     * Publish to Endpoint
     *
     * @link http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Sns.SnsClient.html#_publish
     *
     * @param string|array $endpointArn
     * @param array $content
     *
     * @return string|array
     *
     */
    public function publishToEndpoint($endpointArn = '', $content = array())
    {
        $firstEndpoint = null;
        if (is_array($endpointArn)) {
            $total = count($endpointArn);
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to publish message to {$total} end points");
            $firstEndpoint = @$endpointArn[0];
        } else {
            $this->writeDebugLog("Execute " . __FUNCTION__ . " to publish message to {$endpointArn} end point");
            $firstEndpoint = $endpointArn;
        }
        $api = new Api('publish');
        $api->setSnsClient($this->sns_client);
        $arn = new Arn($firstEndpoint); 
        $message = new Message($arn->platform, $content);
        $this->writeDebugLog("Message {$message->getData()}");

        if (is_array($endpointArn)) {
            foreach ($endpointArn as $_endpointArn) {
                $api->addParam([
                    'TargetArn' => $_endpointArn,
                    'MessageStructure' => 'json',
                    'Message' => $message->getData(),
                ]);
            }
        } else {
            $api->addParam([
                'TargetArn' => $endpointArn,
                'MessageStructure' => 'json',
                'Message' => $message->getData(),
            ]);
        }

        return $api->execute();
    }
}
