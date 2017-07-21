<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 21/07/2017
 * Time: 09:06
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Category;
use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 150; ++$i) {
            $post = new Post();
            $post
                ->setName("post$i")
                ->setContent("Nisi mihi Phaedrum, inquam, tu mentitum aut Zenonem putas, quorum utrumque audivi, 
                cum mihi nihil sane praeter sedulitatem probarent, omnes mihi Epicuri sententiae satis notae sunt. 
                atque eos, quos nominavi, cum Attico nostro frequenter audivi, cum miraretur ille quidem utrumque, 
                Phaedrum autem etiam amaret, cotidieque inter nos ea, quae audiebamus, conferebamus, neque erat 
                umquam controversia, quid ego intellegerem, sed quid probarem.");

            $categories = [];
            for ($j = 0; $j < 5; ++$j) {
                $randCategory = mt_rand(0, 20);
                if ($randCategory != 0) {
                    /** @var Category $category */
                    $category = $this->getReference("category_$randCategory");
                    $categories[$category->getId()] = $category;
                }
            }

            foreach ($categories as $id => $category) {
                $post->addCategory($category);
            }

            $randUser = mt_rand(1, 10);
            $post->setUser($this->getReference("user_basic_$randUser"));

            $manager->persist($post);
        }

        $manager->flush();
    }
}