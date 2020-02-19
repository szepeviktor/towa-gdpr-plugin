<?php

/**
 * Hash class file.
 *
 * @author Martin Welte <martin.welte@towa.at>
 * @copyright 2019 Towa
 * @license GPL-2.0+
 */

namespace Towa\GdprPlugin;

use DateTime;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Hash class.
 *
 * @author Martin Welte <martin.welte@towa.at>
 */
class Hash
{
    /**
     * hash value.
     *
     * @var string
     */
    private $hash;

    /**
     * Instantiating the Hash.
     */
    public function __construct()
    {
        $this->generateHash();
    }

    /**
     * get the Hash.
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * generate a hash from the current Datetime.
     *
     * @throws \Exception
     */
    private function generateHash(): void
    {
        $this->hash = md5((string)(new DateTime('now'))->getTimestamp());
    }
}
