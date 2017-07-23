<?php
namespace Ugho\UploadBundle\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use Ugho\UploadBundle\Annotation\Uploadable;
use Ugho\UploadBundle\Annotation\UploadableField;

/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 23/07/2017
 * Time: 12:26
 */
class UploadAnnotationReader
{

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * UploadAnnotationReader constructor.
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function isUploadable($entity): bool {
        $reflection = new \ReflectionClass(get_class($entity));
        return $this->reader->getClassAnnotation($reflection, Uploadable::class) !== null;
    }

    public function getUploadableFields($entity): array {
        if (!$this->isUploadable($entity)) {
            return [];
        }

        $reflection = new \ReflectionClass(get_class($entity));
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, UploadableField::class);

            if ($annotation !== null) {
                $properties[$property->getName()] = $annotation;
            }
        }

        return $properties;
    }
}