<?php

// Quick test to check if mbstring is loaded
echo 'PHP Version: '.PHP_VERSION."\n";
echo 'mbstring extension loaded: '.(extension_loaded('mbstring') ? 'YES ✅' : 'NO ❌')."\n";

if (extension_loaded('mbstring')) {
    echo 'mbstring functions available: '.(function_exists('mb_split') ? 'YES ✅' : 'NO ❌')."\n";
    echo 'mbstring version: '.phpversion('mbstring')."\n";
} else {
    echo "❌ mbstring extension is NOT loaded!\n";
    echo "Please check your php.ini file.\n";
}
