<?php

use Qsoft\Helpers\SubscribeManager;

if (isset($_GET['subscriber_id']) && isset($_GET['email'])) {
    $subscriberID = $_GET['subscriber_id'];
    $email = $_GET['email'];

    $subscriber = SubscribeManager::getSubscriber($subscriberID);
    if ($subscriber && $email == $subscriber['PROPERTY_EMAIL_VALUE']) {
        SubscribeManager::updateSubscriber($subscriberID, false, true);
    }
}