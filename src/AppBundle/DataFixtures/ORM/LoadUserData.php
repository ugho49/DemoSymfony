<?php
/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 21/07/2017
 * Time: 09:06
 */

namespace AppBundle\DataFixtures\ORM;


use AdminBundle\Enum\RolesEnum;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $passwordEncoder = $this->container->get('security.password_encoder');

        $superAdmin = new User();
        $superAdmin
            ->setEmail("stephan.ugho@gmail.com")
            ->setFirstname("Ugho")
            ->setLastname("STEPHAN")
            ->setBirthday(new \DateTime('1993-11-15'))
            ->setRoles([RolesEnum::ROLE_SUPERADMIN]);

        $superAdmin->setPassword($passwordEncoder->encodePassword($superAdmin, "test"));

        $manager->persist($superAdmin);
        $this->addReference('user_superadmin', $superAdmin);

        for ($i = 1; $i <= 3; ++$i) {
            $admin = new User();

            $admin
                ->setEmail("admin_$i@test.com")
                ->setFirstname("Admin$i")
                ->setLastname("ADMIN$i")
                ->setRoles([RolesEnum::ROLE_ADMIN]);

            $admin->setPassword($passwordEncoder->encodePassword($admin, "test"));

            $manager->persist($admin);
            $this->addReference("user_admin_$i", $admin);
        }

        for ($i = 1; $i <= 10; ++$i) {
            $user = new User();

            $user
                ->setEmail("user_$i@test.com")
                ->setFirstname("User$i")
                ->setLastname("USER$i")
                ->setRoles([RolesEnum::ROLE_USER]);

            $user->setPassword($passwordEncoder->encodePassword($user, "test"));

            $manager->persist($user);
            $this->addReference("user_basic_$i", $user);
        }

        $manager->flush();
    }
}