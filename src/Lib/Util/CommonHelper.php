<?php
namespace Lib\Util;

class CommonHelper
{
    /*
     * Get environment by ip address (for linux)
     */
    public static function getEnvironmentByIPAddress()
    {
        $env = Constants::ENV_DEVELOP;
        $output = shell_exec("/sbin/ifconfig $1 | grep 'inet addr' | awk -F: '{print $2}' | awk '{print $1}'");
        $list_ip = preg_split('/\s+/', trim($output));
        if (empty($list_ip)) {
            return $env;
        }
        $list_ip_production = Constants::getListIpAddressProduction();
        foreach ($list_ip as $ip) {
            if (in_array($ip, $list_ip_production)) {
                $env = Constants::ENV_PRODUCT;
                break;
            }
        }

        return $env;
    }

} // end class
