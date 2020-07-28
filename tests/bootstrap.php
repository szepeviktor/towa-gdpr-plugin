<?php

declare(strict_types=1);

namespace Towa\GdprPlugin\Tests;

// Check for a `--testsuite integration` arg when calling phpunit, and use it to conditionally load up WordPress.
$towa_gdpr_plugin_argv = $GLOBALS['argv'];
$towa_gdpr_plugin_key = (int)array_search('--testsuite', $towa_gdpr_plugin_argv, true);
