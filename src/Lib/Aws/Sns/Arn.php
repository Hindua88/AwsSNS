<?php
namespace Lib\Aws\Sns;

/**
 * ARN (Amazon Resource Names) Extractor
 * This will parse arn string and extract it's information
 *
 * General formats for ARNs:
 * arn:partition:service:region:account:resource
 * arn:partition:service:region:account:resourcetype/resource
 * arn:partition:service:region:account:resourcetype:resource
 *
 * Here, we focus in endpoint's arns, example:
 * arn:aws:sns:ap-southeast-1:69696696969:endpoint/APNS_SANDBOX/168dzo_dev/05b339c5-104c-32a4-a039-b0b6c7e40459
 * arn:aws:sns:ap-southeast-1:69696696969:endpoint/APNS/168dzo_pro/01120029-0168-30b2-a2cf-a3b2f93d4fd5
 *
 * @link http://docs.aws.amazon.com/general/latest/gr/aws-arns-and-namespaces.html
 */
class Arn
{
    public $base;

    // Common
    public $partition;
    public $service;
    public $region;
    public $account;
    public $resourcetype;
    public $resource;

    // Sns
    public $platform;
    public $appname;
    public $id;

    public function __construct($arn = '')
    {
        $arn = (string)$arn;

        $this->base = $arn;
        $this->extract($arn);
    }

    /*
        Extract end point to get information

        Ex: arn:aws:sns:ap-southeast-1:69696696969:endpoint/APNS_SANDBOX/168dzo_dev/05b339c5-104c-32a4-a039-b0b6c7e40459
        Result:
        Arn object: {
          ["base"]=>
          string(109) "arn:aws:sns:ap-southeast-1:69696696969:endpoint/APNS_SANDBOX/168dzo_dev/05b339c5-104c-32a4-a039-b0b6c7e40459"
          ["partition"]=>
          string(3) "aws"
          ["service"]=>
          string(3) "sns"
          ["region"]=>
          string(14) "ap-southeast-1"
          ["account"]=>
          string(12) "69696696969"
          ["resourcetype"]=>
          string(8) "endpoint"
          ["resource"]=>
          string(61) "/APNS_SANDBOX/168dzo_dev/05b339c5-104c-32a4-a039-b0b6c7e40459"
          ["platform"]=>
          string(12) "APNS_SANDBOX"
          ["appname"]=>
          string(10) "168dzo_dev"
          ["id"]=>
          string(36) "05b339c5-104c-32a4-a039-b0b6c7e40459"
        }
    */
    private function extract($arn = '')
    {
        if (empty($arn)) {
            return false;
        }

        // Parse paths
        $parse_arn = explode('/', $arn);
        $namespace = @$parse_arn[0];
        if ($parse_arn) {
            $this->resource = str_replace($namespace, '', $arn);
        }
        $this->platform = @$parse_arn[1];
        $this->appname = @$parse_arn[2];
        $this->id = @$parse_arn[3];

        // Parse namespace
        $parse_ns = explode(':', $namespace);
        $this->partition = @$parse_ns[1];
        $this->service = @$parse_ns[2];
        $this->region = @$parse_ns[3];
        $this->account = @$parse_ns[4];

        if (@$parse_ns[5]) {
            if (count($parse_arn) == 1) {
                $this->resource = $parse_ns[5];
            } else {
                $this->resourcetype = $parse_ns[5];
            }
        }
    }
}
