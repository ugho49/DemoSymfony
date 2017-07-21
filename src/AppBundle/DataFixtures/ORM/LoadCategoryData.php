<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 21/07/2017
 * Time: 09:06
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 20; ++$i) {
            $category = new Category();
            $category->setName("category$i");
            $manager->persist($category);
            $this->addReference("category_$i", $category);
        }

        $manager->flush();
    }


}