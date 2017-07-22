<?php
/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 22/07/2017
 * Time: 11:53
 */

namespace AppBundle\Traits;

use AppBundle\Entity\File;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadableSingle
 *
 * Don't forget to add annotation to your class : ORM\HasLifecycleCallbacks
 */
trait UploadableSingle
{
    /**
     * @var UploadedFile
     *
     * @Serializer\Exclude()
     */
    private $uploadedFile;

    /**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\File", cascade={"persist"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $file;

    /**
     * @ORM\PreFlush()
     */
    public function upload()
    {
        if ($this->uploadedFile) {
            $name = sha1(uniqid(mt_rand(), true)) . '.' . $this->uploadedFile->getClientOriginalExtension();

            $file = new File();
            $file->setWebPath($this->getUploadRootDir() . $name);
            $file->setSize($this->uploadedFile->getClientSize());
            $file->setName($name);
            $file->setExtension($this->uploadedFile->getClientOriginalExtension());
            $absoluteDir = $this->getWebDir() . $this->getUploadRootDir();
            $file->setAbsolutePath($absoluteDir);
            $this->uploadedFile->move($absoluteDir, $name);

            $this->setUploadedFile(null);
            $this->setFile($file);
        }
    }

    /**
     * @return string
     */
    protected function getWebDir(): string {
        return WEB_DIR;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir(): string {
        return "/uploads/" . date("Ymd") . "/";
    }

    /**
     * @return UploadedFile|null
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @param $uploadedFile
     * @return $this
     */
    public function setUploadedFile($uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
}