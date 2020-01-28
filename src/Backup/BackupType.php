<?php
/**
 * Created by PhpStorm.
 * User: marti
 * Date: 17.01.2020
 * Time: 17:31
 */

namespace Towa\GdprPlugin\Backup;


class BackupType
{
	private $name;
	private $id;
	private $class;

	/**
	 * BackupType constructor.
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
	 * @return string
	 */
	public function get_class_as_string():string {
		return $this->class;
	}

	/**
	 * Get Intatiated Class of type
	 */
	public function get_intatiate_class(){
		$class = new $this->class;
		return $class;
	}

	public function get_acf_settings(){
		return [
			$this->id => $this->name
		];
	}
}
