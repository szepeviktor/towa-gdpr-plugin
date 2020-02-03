<?php

/**
 * FTP Backup File
 *
 * @author Martin Welte
 * @copyright 2020 Towa
 */

namespace Towa\GdprPlugin\Backup;

use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Towa\Acf\Fields\Textarea;

/**
 * Class FtpBackup
 *
 * @package Towa\GdprPlugin\Backup
 */
class FtpBackup implements BackupInterface
{

	protected const ACF_PREFIX = 'backup_ftp';
	protected const INI_SETTINGS_KEY = 'ftp_general_settings';

	/**
	 * Get Name for ACF Fields
	 *
	 * @param $prefix
	 * @return string
	 */
	final public function getAcfName($prefix){
		return $prefix . '_' . self::ACF_PREFIX;
	}

	/**
	 * get FTP Adapter
	 *
	 * @return Ftp
	 */
	private function getAdapter(){
		return new Ftp($this->getConfig());
	}

	/**
	 * get Config from Settings ACF Field
	 *
	 * @return array
	 */
	protected function getConfig(): array{
		$options = get_field(self::INI_SETTINGS_KEY,'option');
		$settings = parse_ini_string($options);
		return $settings;
	}

	/**
	 * get Local Filesystem Adapter
	 *
	 * @return Local
	 */
	protected function getLocalAdapter(){
		$file_path = WP_CONTENT_DIR . '/uploads/towa-gdpr/';
		// ToDo use Consent Path Constant
		return new Local($file_path);
	}

	/**
	 * Mount Filesystems with local and remote Adapter
	 *
	 * @return MountManager
	 */
	protected function mountFilesystems(): MountManager{
		$remoteFilesystem = new Filesystem($this->getAdapter());
		$localFileSystem = new Filesystem($this->getLocalAdapter());
		$manager = new MountManager([
			'local' => $localFileSystem,
			'remote' => $remoteFilesystem
		]);

		return $manager;
	}

	/**
	 * Save File of date via FTP
	 *
	 * @param \DateTime $date
	 * @return bool|void
	 * @throws \League\Flysystem\FileExistsException
	 */
	public function save(\DateTime $date)
	{
		$fileName = $date->format('d-m-Y') . '.csv';
		$remoteFile = 'remote://' . $fileName;
		$localFile = 'local://' . $fileName;
		$manager = $this->mountFilesystems();
		if($manager->has($remoteFile)){
			$manager->delete($remoteFile);
		}
		$manager->copy($localFile,$remoteFile);
	}

	/**
	 * Save all Files
	 *
	 * @return int Filecount
	 */
	public function saveAll():int {
		$manager = $this->mountFilesystems();
		$filesCollection = collect(($this->getLocalAdapter())->listContents());
		$filesCollection->each(function($file) use ($manager) {
			$localFile = 'local://' . $file['path'];
			$remoteFile = 'remote://' . $file['path'];
			if($manager->has($remoteFile)){
				$manager->delete($remoteFile);
			}
			$manager->copy($localFile,$remoteFile);
		});

		return $filesCollection->count();
	}

	/**
	 * remove all Local Files
	 *
	 * @return int Filecount
	 */
	final public function removeLocalFiles():int {
		$localAdapter = $this->getLocalAdapter();
		$filesCollection = collect($localAdapter->listContents());
		$filesCollection->each(function($file) use ($localAdapter) {
			$localAdapter->delete($file['path']);
		});

		return $filesCollection->count();
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
