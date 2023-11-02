<?php

$config = array();

// Site Names...
$config['siteName'] = "FiveM Script Versions";

// Languages

$config['availableLangs'] = ['en', 'es'];
$config['defaultLang'] = 'en';
$config['userLang'] = in_array(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), $config['availableLangs']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : $config['defaultLang'];


// Users config

$config['allowRegistrations'] = true;
