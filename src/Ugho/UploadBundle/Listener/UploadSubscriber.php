<?php
/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 23/07/2017
 * Time: 12:37
 */

namespace Ugho\UploadBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ugho\UploadBundle\Annotation\UploadableField;
use Ugho\UploadBundle\Handler\UploadHandler;
use Ugho\UploadBundle\Reader\UploadAnnotationReader;

class UploadSubscriber implements EventSubscriber
{

    /**
     * @var UploadAnnotationReader
     */
    private $reader;
    /**
     * @var UploadHandler
     */
    private $handler;

    /**
     * UploadSubscriber constructor.
     * @param UploadAnnotationReader $reader
     * @param UploadHandler $handler
     */
    public function __construct(UploadAnnotationReader $reader, UploadHandler $handler)
    {
        $this->reader = $reader;
        $this->handler = $handler;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postUpdate,
            Events::preRemove,
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event) {
        $entity = $event->getEntity();
        $em = $event->getEntityManager();

        /**
         * @var string $property
         * @var UploadableField $annotation
         */
        foreach ($this->reader->getUploadableFields($entity) as $property => $annotation) {
            $this->handler->upload($entity, $property, $annotation, $em);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event) {

        $entity = $event->getEntity();
        $em = $event->getEntityManager();

        /**
         * @var string $property
         * @var UploadableField $annotation
         */
        foreach ($this->reader->getUploadableFields($entity) as $property => $annotation) {
            //$this->handler->removeFile($entity, $annotation->getFileProperty(), $em);
            $this->handler->upload($entity, $property, $annotation, $em);
        }

        //dump($entity);
        //throw new \Exception("rrd");//
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event) {

        $entity = $event->getEntity();
        $em = $event->getEntityManager();

        /**
         * @var string $property
         * @var UploadableField $annotation
         */
        foreach ($this->reader->getUploadableFields($entity) as $property => $annotation) {
            //$this->handler->removeFile($entity, $annotation->getFileProperty(), $em);
        }
    }
}