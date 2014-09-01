<?php
use \Punic\Unit;

include '../vendor/autoload.php';

// This will output `2 Millisekunden`
echo Unit::format(2, 'millisecond', 'long', 'de');

// This will output `2 ms`
echo Unit::format(2, 'millisecond', 'short', 'en');
