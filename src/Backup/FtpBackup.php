<?php
/**
 * Created by PhpStorm.
 * User: marti
 * Date: 20.01.2020
 * Time: 14:08
 */

namespace Towa\GdprPlugin\Backup;


use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Towa\Acf\Fields\Textarea;

class FtpBackup implements BackupInterface
{
	protected const ACF_PREFIX = 'backup_ftp';
	protected const INI_SETTINGS_KEY = 'ftp_general_settings';

	public function getAcfName($prefix){
		return $prefix.'_'.self::ACF_PREFIX;
	}

	private function getAdapter(){
		return new Ftp($this->getConfig());
	}

	protected function getConfig(): array{
		$options = get_field(self::INI_SETTINGS_KEY,'option');
		$settings = parse_ini_string($options);
		return $settings;
	}

	protected function getLocalAdapter(){
		$file_path = WP_CONTENT_DIR . '/uploads/towa-gdpr/';
		// ToDo use Consent Path Constant
		return new Local($file_path);
	}

	public function save(\DateTime $date)
	{
		$remoteFilesystem = new Filesystem($this->getAdapter());
		$localFileSystem = new Filesystem($this->getLocalAdapter());
		$manager = new MountManager([
			'local' => $localFileSystem,
			'remote' => $remoteFilesystem
		]);
		$manager->copy(
			'local://'.$date->format('d-m-Y').'.csv',
			'remote://'.$date->format('d-m-Y').'.csv'
		);
	}

	public function getSettingsFields(string $prefix): array
	{
		// add additional Settings with conditional fields, if you need to
		return [
			(new Textarea($this->getAcfName($prefix), self::INI_SETTINGS_KEY,__('Settings','towa-gdpr')))->build([
				'instructions' => 'Example FTP settings: <br/> host=ftp.exampleurl.com <br/> root=/ </br> user=exampleuser </br/> password=examplepassword <br/> <a href="https://flysystem.thephpleague.com/v1/docs/adapter/ftp/" target="_blank">all Prameters</a>',
				'new_lines' => "\n",
				'conditional_logic' => [
					[
						[
							'field' => $prefix . '_backup_type',
							'operator' => '==',
							'value' => 'ftp'
						]
					]
				]
			])
		];
	}

}
