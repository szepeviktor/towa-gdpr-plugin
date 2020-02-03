<?php

/**
 * Backup Type
 *
 * @author       Martin Welte
 * @copyright    2020 Towa
 * @license      GPL-2.0+
 */

namespace Towa\GdprPlugin\Backup;

use Towa\GdprPlugin\Plugin;

/**
 * Class BackupType.
 *
 * @package Towa\GdprPlugin\Backup
 */
class BackupType
{
	private $name;
	private $id;
	private $class;

	/**
	 * BackupType constructor.
	 *
	 * @param array $typeAsArray
	 */
	public function __construct(array $typeAsArray)
	{
		if(isset($typeAsArray['name'])){
			$this->name = $typeAsArray['name'];
		}
		if(isset($typeAsArray['id'])){
			$this->id = $typeAsArray['id'];
		}
		if(isset($typeAsArray['id'])){
			$this->class = $typeAsArray['class'];
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_name():string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_id():string {
		return $this->id;
	}

	/**
	 * Get Class of Backuptype as String
	 *
	 * @return string
	 */
	public function get_class_as_string():string {
		return $this->class;
	}

	/**
	 * Get initiated Class of Backuptype
	 *
	 * @return BackupInterface
	 */
	public function get_intatiate_class(): BackupInterface{
		$class = new $this->class;
		return $class;
	}

	/**
	 * Get Acf Settings of Backup Type
	 *
	 * @return array
	 */
	public function get_acf_settings(): array {
		return [
			$this->id => $this->name
		];
	}
}
