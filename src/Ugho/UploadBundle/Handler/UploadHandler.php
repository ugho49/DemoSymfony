<?php
/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 23/07/2017
 * Time: 14:58
 */

namespace Ugho\UploadBundle\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Ugho\UploadBundle\Annotation\UploadableField;
use Ugho\UploadBundle\Entity\File;

class UploadHandler
{
    /**
     * @var PropertyAccessor
     */
    private $accessor;
    /**
     * @var string
     */
    private $webRootDir;

    /**
     * UploadHandler constructor.
     * @param string $kernelRootDir
     * @param EntityManager $em
     */
    public function __construct(string $kernelRootDir)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->webRootDir = $kernelRootDir . "/../web";
    }

    /**
     * @param $entity
     * @param string $property
     * @param UploadableField $annotation
     * @param EntityManager $em
     */
    public function upload($entity, string $property, UploadableField $annotation, EntityManager $em) {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $this->accessor->getValue($entity, $property);

        if ($uploadedFile instanceof UploadedFile) {
            $name = sha1(uniqid(mt_rand(), true)) . '.' . $uploadedFile->getClientOriginalExtension();
            $file = new File();
            $file->setWebPath($annotation->getUploadPath() . '/' . $name);
            $file->setSize($uploadedFile->getClientSize());
            $file->setName($name);
            $file->setExtension(strtoupper($uploadedFile->getClientOriginalExtension()));
            $absoluteDir = $this->webRootDir . $annotation->getUploadPath();
            $file->setAbsolutePath($absoluteDir . "/");

            $em->persist($file);

            $this->accessor->setValue($entity, $annotation->getFileProperty(), $file);

            $uploadedFile->move($absoluteDir, $name);
        }
    }

    /**
     * @param $entity
     * @param string $filePropertyName
     * @param EntityManager $em
     */
    public function removeFile($entity, string $filePropertyName, EntityManager $em) {
        $file = $this->accessor->getValue($entity, $filePropertyName);

        if ($file !== null) {
            $em->remove($file);
        }
    }
}