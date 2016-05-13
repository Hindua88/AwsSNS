<?php
namespace App\Shell;

require_once APP . 'Lib' . DS . 'Aws' . DS . 'Sns' . DS . 'autoload.php';

use Cake\Console\Shell;
use Cake\Log\Log;
use Lib\Aws\Sns\SnsMobileClient as SnsMobileClient;

/**
 * Push notification message to the endpoints shell using AWS SNS
 */
class PushShell extends Shell
{
    private $sns_client;

    public function initialize()
    {
        parent::initialize();

        // Read config from file or database
#        $sns_key = 'AKIAJWRXPA4L4HTKIPQQ';
#        $sns_secret = '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';
#        $sns_region = 'ap-southeast-1';
#        $platform_application = 'arn:aws:sns:ap-southeast-1:69696696969:app/APNS_SANDBOX/168dzo_dev';

        # My account free aws
        $sns_key = 'AKIAIVTHU7R7V3YXMVBQ';
        $sns_secret = '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';
        $sns_region = 'ap-southeast-1';
        $platform_application = 'arn:aws:sns:ap-southeast-1:634501430222:app/APNS_SANDBOX/My168dzo';

        $this->sns_client = new SnSMobileClient($sns_key, $sns_secret, $sns_region, $platform_application);
    }

    /*
     * Content message push
     *
     * @param string $text
     *
     * return array (custom for project)
     Ex: {
         "default": "Alo", 
         "APNS": "{\"aps\":{\"alert\": \"Alo\"} }", 
         "APNS_SANDBOX":"{\"aps\":{\"alert\":\"Alo\"}}", 
         "GCM": "{ \"data\": { \"message\": \"Alo\" } }", 
     }
     */
    private function getMessage($text = '', $url = '')
    {
        // Example For Android
#        $message = array(
#            'default' => $text,
#            'data' => array(
#                'message' => array(
#                    'type' => 'info',
#                    'message' => $text,
#                    'open_screen' => $url,
#                )
#            )
#        );

        // Example For iOS
        $message = array(
            'aps' => array(
                'alert' => addslashes($text),
                'badge' => 1,
                'sound' => 'default'
            ),
            'url' => $url
        );
        //

        return $message;
    }

    public function main()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

//        $result = $this->sns_client->listPlatformApplications();
//       $result = $this->sns_client->listEndpointsByPlatformApplication();
//        $result = $this->sns_client->getPlatformApplicationAttributes();
//        $result = $this->sns_client->getEndpointAttributes('arn:aws:sns:ap-southeast-1:69696696969:endpoint/APNS/m90_prod/01457f51-f784-3356-b142-ebf5be0410e4');
//        $result = $this->sns_client->listTopics();
//        var_dump($result);
//        $result = $this->sns_client->listTopics($result['NextToken']);
//        $result = $this->sns_client->getTopicName('daudm');
//        var_dump($result);

//        $topic_arn = $this->createTopic();
        $token_6s = '5c65333fdf075cf62f72919a4b7533164d3b3e89acaf27d33c5c775bb63a42f1';
        $end_point = $this->createPlatformEndpoint($token_6s);

//        $subscribe = $this->sns_client->subscribe($topic_arn, $end_point);
//        $this->sns_client->writeDebugLog("Subcribe Id: {$subscribe}");


        $message = $this->getMessage('168 Dzo sắp ra mắt', '/sns?nmenu=1');

        $this->sns_client->publishToEndpoint($end_point, $message);

    }

    private function createPlatformEndpoint($token)
    {
        $result =  $this->sns_client->createPlatformEndpoint($token);
//        $this->sns_client->writeDebugLog("End point: {$result}");
        return $result;
    }

    private function createTopic()
    {
        $topicName = $this->sns_client->getTopicName('demo_topic');
        $this->sns_client->writeDebugLog("Create topic: {$topicName}");
        $result = $this->sns_client->createTopic($topicName);
 //       $this->sns_client->writeDebugLog("Result: {$result}");
        return $result;
    }
}
