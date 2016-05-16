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
        // Ex:  My account free aws
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
            'default' => $text, // requried
            'aps' => array(
                'alert' => $text,
                'badge' => 1,
                'sound' => 'default'
            ),
            'url' => $url
        );

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
        #$topicName = $this->sns_client->getTopicName('demo_topic');

        $topicName = $this->sns_client->getTopicName('demo_test');
//        $topicArn = $this->sns_client->createTopic($topicName);
#        $this->sns_client->deleteTopic($topicArn);

        $token_5s = '115ef84197a39ee1ece9bb9b52d6891b5ad70b35ebe1bb1c20fa78dd952343b1';
        $token_6splus = '337db0f8b98331f33dab1a2265b5937fb7fa829b05f8100af72523fa2288afce';
        $tokens = array($token_5s, $token_6splus);
//        $tokens = array($token_6splus);
        $tokens = $token_6splus;

//        $end_point = $this->sns_client->createPlatformEndpoint($token_5s);
        $end_points = $this->sns_client->createPlatformEndpoint($tokens);
//        var_dump($end_points);
        
//        $result = $this->sns_client->deleteEndpoint($endpointArns);
//        var_dump($result);

//        $this->sns_client->deleteEndpoint($end_point);
//        $subscribes = $this->sns_client->subscribe($topicArn, $end_points);


//        $result = $this->sns_client->unsubscribe($subscribes);
//        var_dump($result);

        $message = $this->getMessage('168 Dzo sắp ra mắt', '/news?nmenu=3');
        $this->sns_client->publishToEndpoint($end_points, $message);
//        $this->sns_client->publishToTopic($topicArn, $message);
    }
}
