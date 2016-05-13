<?php
namespace Lib\Aws\Sns;

class Message
{
    private $platform; // APNS, APNS_SANDBOX or GCM
    private $message; // array

    /*
     * Construct push message
     *
     * @param string $platform
     * @parm array $message
     *
     * @return null
     */
    public function __construct($platform, $message = array())
    {
        $this->platform = $platform;
        $platforms = array(Platform::APNS, Platform::APNS_SANDBOX, Platform::GCM);
        if (! in_array($this->platform, $platforms)) {
            throw new \Exception("Platform {$this->platform} is not support!");
        }
        if (empty($message)) {
            throw new \Exception("message is empty!");
        } elseif (! is_array$message)) {
            throw new \Exception("Message must be an array!");
        }
        $this->message = $message;
    }

    /*
     * Get data 
     *
     * @return string
     */
    public function getData()
    {
        return json_encode(array(
            $this->platform => json_encode($this->message)
        ));
    }
}
