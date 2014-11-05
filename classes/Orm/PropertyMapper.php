<?php
namespace AppZap\PHPFramework\Orm;

use AppZap\PHPFramework\Domain\Model\AbstractModel;
use AppZap\PHPFramework\Utility\Nomenclature;

class PropertyMapper {

  /**
   * @param $source
   * @param $target
   * @return mixed
   * @throws \Exception
   */
  public function map($source, $target) {
    if ($source instanceof $target) {
      return $source;
    }
    $target = ltrim($target, '\\');
    $original_target = $target;
    $value = NULL;
    while(TRUE) {
      switch ($target) {
        case 'AppZap\\PHPFramework\\Domain\\Model\\AbstractModel':
          $value = $this->mapToModel($source, $original_target);
          break(2);
        case 'DateTime':
          $value = $this->mapToDateTime($source, $original_target);
          break(2);
        default:
          $target = get_parent_class($target);
          if ($target === FALSE) {
            throw new PropertyMappingException('No conversion found for type "' . $original_target . '"', 1409745080);
          }
      }
    }
    return $value;
  }

  /**
   * @param int $source
   * @return \DateTime
   */
  protected function mapToDateTime($source, $original_target) {
    if (is_numeric($source)) {
      $timestamp = (int)$source;
      /** @var \DateTime $dateTime */
      $dateTime = new $original_target();
      $dateTime->setTimestamp($timestamp);
      return $dateTime;
    } else {
      return $source;
    }
  }

  /**
   * @param int $source
   * @param string $target_class
   * @return AbstractModel
   */
  protected function mapToModel($source, $target_class) {
    $repository_classname = Nomenclature::modelclassname_to_repositoryclassname($target_class);
    if (!class_exists($repository_classname)) {
      throw new PropertyMappingException('Repository class ' . $repository_classname . ' for model ' . $target_class . ' does not exist.', 1409745296);
    }
    /** @var \AppZap\PHPFramework\Domain\Repository\AbstractDomainRepository $repository */
    $repository = $repository_classname::get_instance();
    return $repository->find_by_id((int) $source);
  }

}
