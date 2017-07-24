<?php
/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 22/07/2017
 * Time: 11:53
 */

namespace AppBundle\Traits;

use AppBundle\Entity\File;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
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
    protected $uploadedFile;

    /**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\File", cascade={"persist"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $file;

    /**
     * @ORM\PreFlush()
     * @param PreFlushEventArgs $eventArgs
     */
    public function _PreFlush(PreFlushEventArgs $eventArgs)
    {
        if ($this->uploadedFile) {
            $this->removeFile($eventArgs->getEntityManager());
            $this->uploadFile();
        }
    }

    /**
     * @ORM\PreRemove()
     * @param LifecycleEventArgs $eventArgs
     */
    public function _PreRemove(LifecycleEventArgs $eventArgs) {
        $this->removeFile($eventArgs->getEntityManager());
    }

    /**
     * Upload File
     */
    private function uploadFile() {
        $name = sha1(uniqid(mt_rand(), true)) . '.' . $this->uploadedFile->getClientOriginalExtension();

        $file = new File();
        $file->setWebPath($this->getUploadRootDir() . $name);
        $file->setSize($this->uploadedFile->getClientSize());
        $file->setName($name);
        $file->setExtension(strtoupper($this->uploadedFile->getClientOriginalExtension()));
        $absoluteDir = $this->getWebDir() . $this->getUploadRootDir();
        // For windows servers
        $absoluteDir = str_replace("/", DIRECTORY_SEPARATOR, $absoluteDir);
        $file->setAbsolutePath($absoluteDir);
        $this->uploadedFile->move($absoluteDir, $name);
        $this->setUploadedFile(null);
        $this->setFile($file);
    }

    /**
     * Remove file if exist
     *
     * @param EntityManager $em
     */
    private function removeFile(EntityManager $em) {
        if ($this->file !== null) {
            $em->remove($this->file);
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