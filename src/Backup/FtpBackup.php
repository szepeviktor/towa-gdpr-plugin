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
use League\Flysystem\Exception;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Towa\Acf\Fields\Text;
use Towa\Acf\Fields\Url;

class FtpBackup implements BackupInterface
{
	private $acf_name = 'towa_gdpr_backup_local';

	private function get_adapter(){
		$this->get_config();
		return new Ftp();
	}

	private function get_config(){
		return [
			'host' => get_field($this->acf_name.'_ftp_host')
		];
	}

	private function get_local_adapter(){
		$file_path = wp_upload_dir(). 'towa-gdpr-plugin/';
		return new Local($file_path);
	}

	public function save(\DateTime $date, $contents)
	{
		$remoteFilesystem = new Filesystem($this->get_adapter());
		$localFileSystem = new Filesystem($this->get_local_adapter());
		$manager = new MountManager([
			'local' => $localFileSystem,
			'remote' => $remoteFilesystem
		]);
		$contents = $manager->read('local://'.$date->getTimestamp().'.csv');
		$manager->write('remote://'.$date->getTimestamp().'.csv',$contents);
	}

	public function get_settings_fields(string $prefix): array
	{
		return [
			(new Text($this->acf_name, 'ftp_host', __('FTP Host', 'towa_gdpr_plugin')))->build([
				'conditional_logic' => [
					[
						'field' => $prefix . '_backup_type',
						'operator' => '==',
						'value' => 'ftp'
					]
				]
			]),
			(new Text($this->acf_name, 'ftp_user', __('FTP user', 'towa_gdpr_plugin')))->build([
				'conditional_logic' => [
					[
						'field' => $prefix . '_backup_type',
						'operator' => '==',
						'value' => 'ftp'
					]
				]
			]),
			(new Text($this->acf_name, 'ftp_password', __('FTP password', 'towa_gdpr_plugin')))->build([
				'conditional_logic' => [
					[
						'field' => $prefix . '_backup_type',
						'operator' => '==',
						'value' => 'ftp'
					]
				]
			])

		];
	}

}