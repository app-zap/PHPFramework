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
    $originalTarget = $target;
    $value = NULL;
    while(TRUE) {
      switch ($target) {
        case 'AppZap\\PHPFramework\\Domain\\Model\\AbstractModel':
          $value = $this->mapToModel($source, $originalTarget);
          break(2);
        case 'DateTime':
          $value = $this->mapToDateTime($source, $originalTarget);
          break(2);
        default:
          $target = get_parent_class($target);
          if ($target === FALSE) {
            throw new PropertyMappingException('No conversion found for type "' . $originalTarget . '"', 1409745080);
          }
      }
    }
    return $value;
  }

  /**
   * @param int $source
   * @param string $dateTimeClassName
   * @return \DateTime
   */
  protected function mapToDateTime($source, $dateTimeClassName) {
    if (is_numeric($source)) {
      $timestamp = (int)$source;
      /** @var \DateTime $dateTime */
      $dateTime = new $dateTimeClassName();
      $dateTime->setTimestamp($timestamp);
      return $dateTime;
    } else {
      return $source;
    }
  }

  /**
   * @param int $source
   * @param string $targetModelClassname
   * @return AbstractModel
   * @throws PropertyMappingException
   */
  protected function mapToModel($source, $targetModelClassname) {
    $repositoryClassname = Nomenclature::modelClassnameToRepositoryClassname($targetModelClassname);
    if (!class_exists($repositoryClassname)) {
      throw new PropertyMappingException('Repository class ' . $repositoryClassname . ' for model ' . $targetModelClassname . ' does not exist.', 1409745296);
    }
    /** @var \AppZap\PHPFramework\Domain\Repository\AbstractDomainRepository $repository */
    $repository = $repositoryClassname::getInstance();
    return $repository->findById((int) $source);
  }

}
