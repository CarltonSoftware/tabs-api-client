<?php

require_once 'tabs/autoload.php';

try {
    
    \tabs\api\client\ApiClient::factory('http://carltonsoftware.apiary.io/');

} catch (Exception $e) {
    echo $e->getMessage();
}