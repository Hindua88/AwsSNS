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
 * arn:aws:sns:us-east-1:492499697490:endpoint/APNS_SANDBOX/iballtest/46405ab2-c446-3a20-952b-9d17b29ec6cf
 *
 * @link http://docs.aws.amazon.com/general/latest/gr/aws-arns-and-namespaces.html
 */
class Arn
{
    public $base;

    // COMMON
    public $partition;
    public $service;
    public $region;
    public $account;
    public $resourcetype;
    public $resource;

    // SNS
    public $platform;
    public $appname;
    public $id;

    public function __construct($arn = '')
    {
        $arn = (string)$arn;

        $this->base = $arn;
        $this->extract($arn);
    }

    private function extract($arn = '')
    {
        if (!is_string($arn) || !trim($arn)) {
            return false;
        }

        // Parse paths
        $parse_arn = explode('/', $arn);

        if (isset($parse_arn[0])) {
            $namespace = $parse_arn[0];

            if (count($parse_arn) > 1) {
                $this->resource = str_replace($namespace, '', $arn);
            }
        }

        if (isset($parse_arn[1])) {
            $this->platform = $parse_arn[1];
        }

        if (isset($parse_arn[2])) {
            $this->appname = $parse_arn[2];
        }

        if (isset($parse_arn[3])) {
            $this->id = $parse_arn[3];
        }

        // Parse namespace
        $parse_ns = explode(':', $namespace);

        if (isset($parse_ns[1])) {
            $this->partition = $parse_ns[1];
        }

        if (isset($parse_ns[2])) {
            $this->service = $parse_ns[2];
        }

        if (isset($parse_ns[3])) {
            $this->region = $parse_ns[3];
        }

        if (isset($parse_ns[4])) {
            $this->account = $parse_ns[4];
        }

        if (isset($parse_ns[5])) {
            if (count($parse_arn) == 1) {
                $this->resource = $parse_ns[5];
            } else {
                $this->resourcetype = $parse_ns[5];
            }
        }
    }
}
