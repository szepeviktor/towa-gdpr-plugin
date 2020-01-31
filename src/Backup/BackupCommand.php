<?php


namespace Towa\GdprPlugin\Backup;


class BackupCommand
{
	public function __construct()
	{
	}

	public function hello($args, $assoc_args){
		\WP_CLI::success('Backup is correctly loaded!');
	}

	public function backup($args){
		$backup = (new Backup())->runBackupOfToday();
		\WP_CLI::line('starting backup process');
		\WP_CLI::success( 'everything backed up' );
	}
}
