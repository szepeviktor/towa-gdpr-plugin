<?php
namespace Towa\GdprPlugin\Backup;

use BrightNucleus\Config\ConfigFactory;
use BrightNucleus\Config\ConfigTrait;
use Illuminate\Support\Collection;

class Backup{

	use ConfigTrait;

	/**
	 * Get Backup Types
	 *
	 * @return Collection
	 */
	public static function get_types(): Collection{
		$types = ConfigFactory::create( TOWA_GDPR_PLUGIN_DIR . '/config/defaults.php' )->getSubConfig( 'Towa\GdprPlugin\Backup' )->getKey('types');
		$additonal_types = do_action('towa_gdpr_add_backup_types');
		$types = collect($types)->merge($additonal_types)->map(function($typeAsArray){
			return new BackupType($typeAsArray);
		})->values();
		return $types;
	}

	/**
	 * Get Types as Array for Acf Select Field
	 *
	 * @return array
	 */
	public static function get_types_for_acf(): array{
		return self::get_types()->flatMap(function($type){
			return $type->get_acf_settings();
		})->all();
	}

	public static function get_conditional_acf_settings(string $prefix): array{
		$test = self::get_types()->flatMap(function($type) use($prefix){
			return $type->get_intatiate_class()->get_settings_fields($prefix);
		})->all();
		return $test;
	}

	private function schedule_backup(){

	}
	private function run_backup(){

	}

}
