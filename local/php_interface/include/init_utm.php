<?php

function setUTMDataInSession()
{
    setcookie('utm_source', $_GET['utm_source'] ?: '', time()+60*60*24*14, '/');
    setcookie('utm_medium', $_GET['utm_medium'] ?: '', time()+60*60*24*14, '/');
    setcookie('utm_campaign', $_GET['utm_campaign'] ?: '', time()+60*60*24*14, '/');
    setcookie('utm_content', $_GET['utm_content'] ?: '', time()+60*60*24*14, '/');
    setcookie('utm_term', $_GET['utm_term'] ?: '', time()+60*60*24*14, '/');
}

if (isset($_GET['utm_source']) || isset($_GET['utm_medium']) || isset($_GET['utm_campaign']) || isset($_GET['utm_content']) || isset($_GET['utm_term'])) {
    setUTMDataInSession();
}
