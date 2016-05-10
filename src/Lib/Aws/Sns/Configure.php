<?php
namespace Lib\Aws\Sns;

/**
 * Configure Class
 *
 */
class Configure
{
    const CONFIG_FILE = 'aws_sns';

    public function __construct()
    {
        \Cake\Core\Configure::load(self::CONFIG_FILE, 'default');
    }

    public static function read($name) {
        $instance = new Configure();
        return $instance->{$name}();
    }

    public static function getPlatformApplicationArn($platform = '')
    {
        $instance = new Configure();

        switch ($platform)
        {
            case Platform::APNS:
            break;

            case Platform::APNS_SANDBOX:
                $platformApplicationArn = $instance->apnsSandboxApplicationArn();
            break;

            case Platform::ADM:
            break;

            case Platform::GCM:
                $platformApplicationArn = $instance->gcmApplicationArn();
            break;

            case Platform::BAIDU:
            break;

            case Platform::WNS:
            break;

            case Platform::MPNS:
            break;

            default:
                throw new \Exception('Platform ' . ($platform ? $platform : 'NULL') . ' is not supported.');
            break;
        };

        return $platformApplicationArn;
    }

    public function app()
    {
        return \Cake\Core\Configure::read('sns_app');
    }

    public function platformApplication()
    {
        return \Cake\Core\Configure::read('platform_application');
    }

    public function listSupportApplication()
    {
        return array_keys($this->platformApplication());
    }

    public function apnsSandboxApplicationArn()
    {
        $platformApplication = $this->platformApplication();
        return $platformApplication[Platform::APNS_SANDBOX];
    }

    public function gcmApplicationArn()
    {
        $platformApplication = $this->platformApplication();
        return $platformApplication[Platform::GCM];
    }
}
