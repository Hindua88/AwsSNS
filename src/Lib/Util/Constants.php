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

    public static function getListIPProduction()
    { 
        return 
            array(
                '172.31.5.11',
            );
    }
}
