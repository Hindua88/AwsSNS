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
    // Amazon Device Messaging
    const ADM = 'ADM';
    // Google Cloud Messaging
    const GCM = 'GCM';
    // Baidu CloudMessaging Service
    const BAIDU = 'BAIDU';
    // Windows Notification Service
    const WNS = 'WNS';
    // Microsoft Push Notificaion Service
    const MPNS = 'MPNS';
}
