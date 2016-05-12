<?php
namespace Lib\Aws\Sns;

/**
 * Abstract Platform Class
 *
 */
class Platform
{
    /* List Platform */
    // Apple Push Notification Service
    const APNS = 'APNS';
    // Sandbox version of Apple Push Notification Service
    const APNS_SANDBOX = 'APNS_SANDBOX';
    // Google Cloud Messaging
    const GCM = 'GCM';
}
