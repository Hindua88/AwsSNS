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

        $sns_key = 'AKIAIFMZ7LG5EWXEUC6A';
        $sns_secret = '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';
        $sns_region = 'ap-southeast-1';
        $platform_application = 'arn:aws:sns:ap-southeast-1:69696696969:app/APNS/m90_prod';

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
        $result = $this->sns_client->listTopics();
//        var_dump($result);
//        $result = $this->sns_client->listTopics($result['NextToken']);
//        $result = $this->sns_client->getTopicName('daudm');
//        var_dump($result);

//        var_dump(ini_get('date.timezone'));

        /*
        $this->sns_client->writeDebugLog('debug log');
        $this->sns_client->writeInfoLog('info log');
        $this->sns_client->writeWarnLog('warn log');
        $this->sns_client->writeErrorLog('error log');
         */
    }
}
