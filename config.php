<?php

$config = array();

// Languages

$config['availableLangs'] = ['en', 'es'];
$config['defaultLang'] = 'en';
$config['userLang'] = in_array(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), $config['availableLangs']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : $config['defaultLang'];
