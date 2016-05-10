<?php
namespace Lib\Aws\Sns;

/**
 * Building message base on platform template
 *
 */
class Message
{
    public $message;
    public $badge;
    public $sound;

    public function __construct()
    {
        $this->message = '';
        $this->badge = 1;
        $this->sound = 'default';
    }

    public function setMessage($message)
    {
        $this->message = (string) $message;
    }

    public function setBadge($badge)
    {
        $this->badge = intval($badge);
    }

    public function setSound($sound)
    {
        $this->sound = (string) $sound;
    }

    public function getApnsSandboxMessage($message)
    {
        $this->setMessage($message);

        return json_encode(array(
            Platform::APNS_SANDBOX => json_encode(array(
                'aps' => array(
                    'alert' => addslashes($this->message),
                    'badge' => $this->badge,
                    'sound' => $this->sound
                ))
            )
        ));
    }

    public function getGcmMessage($message)
    {
        $this->setMessage($message);

        return json_encode(array(
            Platform::GCM => json_encode(array(
                'data' => array(
                    'message' => $this->message,
                ))
            )
        ));
    }

    public function getArnMessage($endpointArn = '', $message = '') {
        $Arn = new Arn($endpointArn);

        switch ($Arn->platform)
        {
            case Platform::APNS:
            break;

            case Platform::APNS_SANDBOX:
                $message = $this->getApnsSandboxMessage($message);
            break;

            case Platform::ADM:
            break;

            case Platform::GCM:
                $message = $this->getGcmMessage($message);
            break;

            case Platform::BAIDU:
            break;

            case Platform::WNS:
            break;

            case Platform::MPNS:
            break;

            default:
                throw new \Exception('Platform ' . $Arn->platform . ' is not supported.');
            break;
        };

        return $message;
    }

    public function getMessage($message)
    {
        $this->setMessage($message);

        $messages = array(
            'default' => addslashes($this->message)
        );

        $listSupportApplication = \Lib\Aws\Sns\Configure::read('listSupportApplication');

        foreach ($listSupportApplication as $application) {
            $application = implode('', array_map('ucfirst', explode('_', $application)));

            $applicationMessage = $this->{"get{$application}Message"}($message);
            $messages += json_decode($applicationMessage, true);
        }

        return json_encode($messages);
    }
}
