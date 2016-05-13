<?php
namespace Lib\Util;

class Constants {

    // Evironment
    const ENV_PRODUCT = 'prod_env';
    const ENV_DEVELOP = 'dev_env';

    // Evironment push for create topic
    const ENV_PRODUCT_PUSH = 'PROD';
    const ENV_DEVELOP_PUSH = 'SANDBOX';

    // For only Android to create topic
    const ENV = Constants::ENV_DEVELOP;

    const PREFIX_TOPIC_NAME_AWS_SNS = 'MY168DZO';

    // Limit number param in an api 
    const LIMIT_MAX_PARAM_API = 1000; // 0: no limit, 1000: 1000

    // Allow to check enviroment push by ip address
    const ALLOW_CHECK_IP_PRODUCTION = 0; // 0: False, 1: True

    // LogLevel:
    //     DEBUG = 1; // Most Verbose
    //     INFO = 2;
    //     WARN = 3;
    //     ERROR = 4;
    //     FATAL = 5; // Least Verbose
    //     OFF = 6; // Nothing at all.
    const LOG_LEVEL = 1;
    const FILE_DEBUG_LOG = '/tmp/aws_sns.log';
    // View request, response api with aws-sns-api
    const FILE_REQUEST_LOG = '/tmp/aws_sns_request.log';

    public static function getListIPProduction()
    { 
        return 
            array(
                '172.31.5.69',
            );
    }
}
