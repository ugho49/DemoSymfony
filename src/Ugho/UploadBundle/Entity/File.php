<?php
/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 22/07/2017
 * Time: 11:46
 */

namespace Ugho\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class File
{

    /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="web_path", type="string", length=255)
     */
    private $webPath;

    /**
     * @var string
     *
     * @ORM\Column(name="absolute_path", type="string", length=255)
     */
    private $absolutePath;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=50)
     */
    private $extension;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Web path
     *
     * @param string $webPath
     *
     * @return File
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;

        return $this;
    }

    /**
     * Get Web path
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * Set Absolute path
     *
     * @param string $absolutePath
     *
     * @return File
     */
    public function setAbsolutePath($absolutePath)
    {
        $this->absolutePath = $absolutePath;

        return $this;
    }

    /**
     * Get Absolute path
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return File
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->name;
    }

    /**
     * @ORM\PreRemove()
     */
    public function removeImage() {
        unlink($this->absolutePath . $this->name);
    }
}
