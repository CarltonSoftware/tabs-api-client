<?php

require_once 'tabs/autoload.php';

try {
    
    \tabs\api\client\ApiClient::factory('http://carltonsoftware.apiary.io/');
    
    $property = \tabs\api\property\Property::getProperty('mousecott', 'SS');
    
    var_dump($property);

} catch (Exception $e) {
    echo $e->getMessage();
}