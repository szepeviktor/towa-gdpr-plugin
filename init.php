<?php

/**
 * Initialise the plugin
 *
 * This file can use syntax from the required level of PHP or later.
 *
 * @author       Martin Welte
 * @copyright    2019 Towa
 * @license      GPL-2.0+
 */

declare(strict_types=1);

namespace Towa\GdprPlugin;

use BrightNucleus\Config\ConfigFactory;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!defined('TOWA_GDPR_PLUGIN_DIR')) {
    define('TOWA_GDPR_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('TOWA_GDPR_PLUGIN_URL')) {
    define('TOWA_GDPR_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('TOWA_GDPR_PLUGIN_FILE')) {
    define('TOWA_GDPR_PLUGIN_FILE', plugin_dir_path(__FILE__) . 'towa-gdpr-plugin.php');
}
// Load Composer autoloader.
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Initialize the plugin.
add_action('plugins_loaded', function () {
    $plugin = new Plugin(ConfigFactory::create(__DIR__ . '/config/defaults.php')->getSubConfig('Towa\GdprPlugin'));
    $plugin->run();
});
