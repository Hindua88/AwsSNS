<?php
namespace Lib\Aws\Sns;

require APP . 'Vendor' . DS . 'aws' . DS . 'autoload.php';

use Aws\Sns\SnsClient;

/**
 * Build sns client instance
 *
 */
class Client
{
    public static function build()
    {
        try
        {
            $Client = SnsClient::factory(\Lib\Aws\Sns\Configure::read('app'));
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }

        return $Client;
    }
}
