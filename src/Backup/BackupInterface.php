<?php
/**
 * BackupInterface
 *
 * @package Towa\GdprPlugin
 * @author  Martin Welte
 * @copyright 2020 Towa
 */

namespace Towa\GdprPlugin\Backup;

/*
 * Interface BackupInterface
 *
 * @package Towa\GdprPlugin
 */
interface BackupInterface
{
	/**
	 * Save current Log file of date to Backuplocation
	 *
	 * @param \DateTime $date
	 * @return bool
	 */
	public function save(\DateTime $date);

	/**
	 * Get Acf Settings Fields necessary for Backupprocess
	 *
	 * @return array
	 */
	public function getSettingsFields(string $prefix): array;

}
