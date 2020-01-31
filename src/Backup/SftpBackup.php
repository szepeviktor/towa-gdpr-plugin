<?php


namespace Towa\GdprPlugin\Backup;


use Towa\Acf\Fields\Textarea;

class SftpBackup extends FtpBackup
{
	protected const ACF_PREFIX = 'backup_sftp';
	protected const INI_SETTINGS_KEY = 'sftp_general_settings';

	private function getAdapter()
	{
		return new Sftp($this->getConfig());
	}

	public function getSettingsFields(string $prefix): array
	{
		return [
			(new Textarea($this->getAcfName($prefix), self::INI_SETTINGS_KEY,__('Settings','towa-gdpr')))->build([
				'instructions' => 'Example SFTP settings: <br/> host=ftp.exampleurl.com <br/> root=/ </br> user=exampleuser </br/> password=examplepassword <br/> <a href="https://flysystem.thephpleague.com/v1/docs/adapter/ftp/" target="_blank">all Prameters</a>',
				'new_lines' => "\n",
				'conditional_logic' => [
					[
						[
							'field' => $prefix.'_backup_type',
							'operator' => '==',
							'value' => 'sftp'
						]
					]
				]
			])
		];
	}
}
