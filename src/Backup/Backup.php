<?php

/**
 * Backup File
 *
 * @author Martin Welte
 * @copyright 2020 Towa
 */

namespace Towa\GdprPlugin\Backup;

use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Config\ConfigTrait;
use Illuminate\Support\Collection;
use Towa\Acf\Fields\Select;
use Towa\Acf\Fields\Tab;
use Towa\Acf\Fields\TrueFalse;

/**
 * Class Backup
 * @package Towa\GdprPlugin\Backup
 */
class Backup{

	use ConfigTrait;

	/**
	 * @var Collection
	 */
	private $allBackupTypes;

	/**
	 * @var BackupType
	 */
	private $backupType;

	/**
	 * Backup constructor.
	 */
	public function __construct()
	{
		$this->allBackupTypes = $this->getTypes();
		$this->backupType = $this->getBackupType();
	}

	/**
	 * init filters of class
	 */
	public function init_filter():void {
		add_filter('towa_gdpr_get_additional_acf_settings',[$this,'getAcfSettings'],10,1);
	}

	/**
	 * get Types of Backups available
	 *
	 * @return Collection
	 */
	private function getTypes(): Collection{
		$types = ConfigFactory::create( TOWA_GDPR_PLUGIN_DIR . '/config/defaults.php' )->getSubConfig( 'Towa\GdprPlugin\Backup' )->getKey('types');
		$additonal_types = do_action('towa_gdpr_add_backup_types');
		$types = collect($types)->merge($additonal_types)->map(function($typeAsArray){
			return new BackupType($typeAsArray);
		})->values();
		return $types;
	}

	/**
	 * Get Type of currently selected BackupInterface
	 *
	 * @return BackupType
	 */
	private function getBackupType(): BackupType{
		$selectedBackuptype = get_field('backup_type','option');
		return $this->allBackupTypes->first(function($item) use($selectedBackuptype){
			return $item->get_id() == $selectedBackuptype;
		});
	}

	/**
	 * Get Types as Array for Acf Select Field
	 *
	 * @return array
	 */
	private function getTypesForAcf(): array
	{
		return $this->allBackupTypes->flatMap(function($type){
			return $type->get_acf_settings();
		})->all();
	}

	/**
	 * Get ACF Settings of Interface Class
	 *
	 * @param string $prefix
	 * @return array
	 */
	public function getAcfSettings(string $prefix): array
	{
		$general_settings = [
			(new Tab($prefix,'tab_backup',__('Consent Logging Backup','towa-gdpr-plugin')))->build(),
			(new TrueFalse($prefix, 'backup_active',__('activate Backup','towa-gdpr-plugin')))->build(),
			(new Select($prefix, 'backup_type', __('Backup type', 'towa-gdpr-plugin')))->build([
					'choices' => $this->getTypesForAcf()
			]),
		];
		$adapter_settings = $this->allBackupTypes->flatMap(function($type) use($prefix){
			return $type->get_intatiate_class()->getSettingsFields($prefix);
		})->all();
		return array_merge( $general_settings, $adapter_settings);
	}

	/**
	 * schedule backup yesterdays Consents
	 */
	public function scheduleBackup():void{
		wp_schedule_event(time(),'daily',[$this, 'runBackupOfYesterday']);
	}

	/**
	 * Backup yesterdays Consent File
	 *
	 * @throws \Exception
	 */
	public function runBackupOfYesterday(){
		$today = new \DateTime();
		$yesterday = $today->add(\DateInterval::createFromDateString('yesterday'));
		($this->backupType->get_intatiate_class())->save($yesterday);
	}

	/**
	 * Run Backup of Specific Date
	 *
	 * @param \DateTime $date
	 */
	public function runBackupOfDay(\DateTime $date){
		($this->backupType->get_intatiate_class())->save($date);
	}

	/**
	 * Backup all Consent Files to Remote Filesystem
	 *
	 * @return int Filecount
	 */
	public function runBackupOfAll(){
		return ($this->backupType->get_intatiate_class()->saveAll());
	}

	/**
	 * Remove all Consent Files from Local Filesystem
	 *
	 * @return int
	 */
	public function removeAllLogFiles(){
		return ($this->backupType->get_intatiate_class())->removeLocalFiles();
	}
}
