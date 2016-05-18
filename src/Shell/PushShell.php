<?php
namespace App\Shell;

require_once APP . 'Library' . DS . 'Aws' . DS . 'Sns' . DS . 'autoload.php';

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
        date_default_timezone_set('Asia/Ho_Chi_Minh');

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
//        $message = array(
//            'default' => $text, // required
//            'data' => array(
//                'message' => array(
//                    'type' => 'info',
//                    'message' => $text,
//                    'open_screen' => $url,
//                )
//            )
//        );

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
        $this->testListPlatformApplications();
//        $this->testGetTopicName();
//        $this->testEndpoint();
//        $this->testTopic();
//         $this->testSubscribe();

//        $this->testPulishToEndpoint();
//        $this->testPulishToTopic();
    }

    private function testPulishToTopic()
    {
        $message = $this->getMessage('168 Dzo sắp ra mắt!', '/news?nmenu=3');
        $token_5s = '115ef84197a39ee1ece9bb9b52d6891b5ad70b35ebe1bb1c20fa78dd952343b1';
        $token_6splus = '337db0f8b98331f33dab1a2265b5937fb7fa829b05f8100af72523fa2288afce';
        $tokens = array($token_5s, $token_6splus);
        $end_points = $this->sns_client->createPlatformEndpoint($tokens);
        $topicName = $this->sns_client->getTopicName('demo_test');
        $topicArn = $this->sns_client->createTopic($topicName);
        var_dump($topicArn);
        $subscribes = $this->sns_client->subscribe($topicArn, $end_points);
        var_dump($subscribes);
        $result = $this->sns_client->publishToTopic($topicArn, $message);
        var_dump($result);
    }

    private function testPulishToEndpoint()
    {
        $message = $this->getMessage('168 Dzô sắp ra mắt', '/news?nmenu=3');
//        $token_5s = '115ef84197a39ee1ece9bb9b52d6891b5ad70b35ebe1bb1c20fa78dd952343b1';
        $token_5s = '5448474e434fe6cf93d2058678c124b8e6340358b9ff9529d7634656255682fb';
        $token_6splus = '337db0f8b98331f33dab1a2265b5937fb7fa829b05f8100af72523fa2288afce';
//        $tokens = array($token_5s, $token_6splus);
        $tokens = $token_5s;
        $end_points = $this->sns_client->createPlatformEndpoint($tokens);
        $result = $this->sns_client->publishToEndpoint($end_points, $message);
        var_dump($result);
    }

    private function testSubscribe()
    {
        $token_5s = '115ef84197a39ee1ece9bb9b52d6891b5ad70b35ebe1bb1c20fa78dd952343b1';
        $token_6splus = '337db0f8b98331f33dab1a2265b5937fb7fa829b05f8100af72523fa2288afce';
        $tokens = array($token_5s, $token_6splus);

        $this->loadModel('User');
        $tokens = $this->User->getTokens(100, 1);
//        var_dump($tokens);
//        var_dump("Total token: " . count($tokens));

        $tokens = $token_5s;
        $start_time = time();
        $end_points = $this->sns_client->createPlatformEndpoint($tokens);
        $total_time = time() - $start_time;
        var_dump("Total time create end point: {$total_time}");

        $topicName = $this->sns_client->getTopicName('demo_test');
        $topicArn = $this->sns_client->createTopic($topicName);
        var_dump($topicArn);

        $start_time = time();
        $subscribes = $this->sns_client->subscribe($topicArn, $end_points);
        $total_time = time() - $start_time;
        var_dump("Total time subscribe end point: {$total_time}");
        var_dump($subscribes);

//        $result = $this->sns_client->unsubscribe($subscribes);
//        var_dump($result);
    }

    private function testListPlatformApplications() {
        $result = $this->sns_client->listPlatformApplications();
        var_dump($result);

       $result = $this->sns_client->listEndpointsByPlatformApplication();
        var_dump($result);

        $result = $this->sns_client->getPlatformApplicationAttributes();
        var_dump($result);
    }

    private function testGetTopicName()
    {
        $result = $this->sns_client->getTopicName('demo_test');
        var_dump($result);
    }

    private function testEndpoint()
    {
        $token_5s = '115ef84197a39ee1ece9bb9b52d6891b5ad70b35ebe1bb1c20fa78dd952343b1';
        $token_6splus = '337db0f8b98331f33dab1a2265b5937fb7fa829b05f8100af72523fa2288afce';
        // one endpoint
        $endpointArn = $this->sns_client->createPlatformEndpoint($token_5s);
        var_dump($endpointArn);
        $result = $this->sns_client->deleteEndpoint($endpointArn);
        var_dump($result);
        // two endpoints
        $endpointArn = $this->sns_client->createPlatformEndpoint(array($token_5s, $token_6splus));
        var_dump($endpointArn);
        $result = $this->sns_client->deleteEndpoint($endpointArn);
        var_dump($result);
        // test enable
        $this->sns_client->setEndpointEnabled($endpointArn);
        $result = $this->sns_client->getEndpointAttributes($endpointArn);
        var_dump($result);
        sleep(5);
        // test disable
        $this->sns_client->setEndpointDisabled($endpointArn);
        $result = $this->sns_client->getEndpointAttributes($endpointArn);
        var_dump($result);
        sleep(5);
        $this->sns_client->setEndpointEnabled($endpointArn);
        $result = $this->sns_client->getEndpointAttributes($endpointArn);
        var_dump($result);
        $message = $this->getMessage('168 Dzo sắp ra mắt', '/news?nmenu=3');
//         $this->sns_client->publishToEndpoint($endpointArn, $message);
    }

    private function testTopic()
    {
        $result = $this->sns_client->listTopics();
        var_dump($result);
        if (@$result['NextToken']) {
            $result = $this->sns_client->listTopics($result['NextToken']);
            var_dump($result);
        }
        $topicName = $this->sns_client->getTopicName('demo_test');
        $topicArn = $this->sns_client->createTopic($topicName);
        $topicName = $this->sns_client->getTopicName('demo_test2');
        $topicArn2 = $this->sns_client->createTopic($topicName);

        $topicArns = array($topicArn, $topicArn2);
        var_dump($topicArns);
        $result = $this->sns_client->deleteTopic($topicArns);
        var_dump($result);
    }
}
