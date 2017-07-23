<?php

namespace Ugho\UploadBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;


/**
 * @Annotation
 * @Target("PROPERTY")
 */
class UploadableField
{

    /**
     * @var string
     */
    private $fileProperty;

    /**
     * @var string
     */
    private $uploadPath = "/uploads";

    /**
     * UploadableField constructor.
     * @param array $options
     */
    function __construct(array $options)
    {
        if ($options["fileProperty"] === null) {
            throw new \InvalidArgumentException("'fileProperty' should not be null");
        }

        if (array_key_exists("uploadPath", $options)) {
            $this->uploadPath = $options["uploadPath"];
        }

        $this->fileProperty = $options["fileProperty"];
    }

    /**
     * @return string
     */
    public function getFileProperty(): string
    {
        return $this->fileProperty;
    }

    /**
     * @return string
     */
    public function getUploadPath(): string
    {
        return $this->uploadPath;
    }
}