<?php

/**
 * Backup Command File
 *
 * @author Martin Welte
 * @copyright 2020 Towa
 */

namespace Towa\GdprPlugin\Backup;

/**
 * Class BackupCommand
 *
 * @package Towa\GdprPlugin\Backup
 */
class BackupCommand
{
	/**
	 * Backup GDPR Consent Files to a remote system. This system is defined in the options of the backend of the website
	 *
	 * --date=<today|yesterday|all>
	 *    today: if you want to backup todays Consents
	 *    yesterday: if you want to backup yesterdays Consents
	 *    all: if you want to backup all existing Consents
	 *
	 * @param $args
	 * @param $assoc_args
	 * @throws \Exception
	 *
	 */
	public function backup($args,$assoc_args){
		if(key_exists('date',$assoc_args)){
			\WP_CLI::confirm('Files on Remote will be overwritten. Are you sure you want to continue?');
			switch ($assoc_args['date']){
				case 'today':
					\WP_CLI::line('starting backup process of <todays> Consents');
					(new Backup())->runBackupOfDay(new \DateTime());
					break;
				case 'yesterday':
					\WP_CLI::line('starting backup process of <YESTERDAYS> Consents');
					(new Backup())->runBackupOfYesterday();
					break;
				case 'all':
					\WP_CLI::line('starting backup process of <ALL> Consents');
					$count = (new Backup())->runBackupOfAll();
					\WP_CLI::success($count . ' Files backed up! %n');
					break;
				default:
					\WP_CLI::error('looks like there was an error with your input. Please check the syntax with --help');
					break;
			}
		}
		else{
			\WP_CLI::error('looks like there was an error with your input. Please check the syntax with --help');
		}
		\WP_CLI::success( 'everything backed up' );
	}

	/**
	 * Delete Local Consent Log files
	 *
	 * @param $args
	 */
	public function deleteLocalLogs($args){
		\WP_CLI::confirm('ALL Consent Logs will be deleted from the local filesystem. Are you sure you want to continue?');
		\WP_CLI::line('Aye captain, deleting Files now...');
		$count = (new Backup())->removeAllLogFiles();
		if($count === 0){
			\WP_CLI::error('wait... there are no files to delete?? Maybe you made a mistake?');
		}
		\WP_CLI::success($count . ' Files were deleted!');
	}
}
