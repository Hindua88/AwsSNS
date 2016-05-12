<?php
namespace Lib\Util;
/*
 * Common Helper
 * @author: dau's
 * @created: 2016/05/10
 */
require_once 'Constants.php';

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
        $list_ip_production = Constants::getListIPProduction();
        foreach ($list_ip as $ip) {
            if (in_array($ip, $list_ip_production)) {
                $env = Constants::ENV_PRODUCT;
                break;
            }
        }

        return $env;
    }

    /*
     * Get site url
     *
     * @return string
     */
    public static function getSiteURL()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];

        return $protocol . $domainName;
    }

    /**
     * Append param to uri
     *
     * @param string $uri
     * @param string $param_name
     * @param string $param_value
     *
     * @return string
     */
    public static function appendParamUri($uri, $param_name, $param_value)
    {
        if (empty($uri)) {
            return $uri;
        }
        if (strpos($uri, '?') !== false) {
            $uri .= "&{$param_name}={$param_value}";
        } else {
            $uri .= "?{$param_name}={$param_value}";
        }

        return $uri;
    }

    /*
     * Get current time
     *
     * @return string
     */
    public static function getCurrentTimeString()
    {
        return date('Y-m-d H:i:s');
    }

    /*
     * Get current time
     *
     * @return string
     */
    public static function getCurrentDateString()
    {
        return date('Y-m-d');
    }

    /**
     * Check substr start string
     *
     * @param $needle
     *
     * @return string
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Check substr end string
     *
     * @param $needle
     *
     * @return string
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

} // end class
