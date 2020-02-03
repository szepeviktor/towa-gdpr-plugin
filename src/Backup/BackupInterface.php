<?php
/**
 * BackupInterface File
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
	 * Save all Files to the Remote Filesystem and return count of Files saved
	 *
	 * @return int CountSavedFiles
	 */
	public function saveAll():int;


	/**
	 * remove all Local Files
	 *
	 * @return int CountDeletedFiles
	 */
	public function removeLocalFiles():int;


	/**
	 * Get Acf Settings Fields necessary for Backupprocess
	 *
	 * @return array
	 */
	public function getSettingsFields(string $prefix): array;


}
