<?php

namespace AppZap\PHPFramework\Persistence;

class DatabaseMigrationContext {

  /**
   * @var string
   */
  protected $directory;

  /**
   * @var int
   */
  protected $lastExecutedVersion;

  /**
   * @var string
   */
  protected $name;

  /**
   * @param string $name
   * @param string $directory
   */
  public function __construct($name, $directory) {
    $this->name = $name;
    $this->directory = $directory;
  }

  /**
   * @return string
   */
  public function getDirectory() {
    return $this->directory;
  }

  /**
   * @param string $directory
   */
  public function setDirectory($directory) {
    $this->directory = $directory;
  }

  /**
   * @return int
   */
  public function getLastExecutedVersion() {
    return $this->lastExecutedVersion;
  }

  /**
   * @param int $lastExecutedVersion
   */
  public function setLastExecutedVersion($lastExecutedVersion) {
    $this->lastExecutedVersion = $lastExecutedVersion;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

}
