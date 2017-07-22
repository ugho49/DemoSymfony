<?php

namespace AppBundle\Entity;

use AppBundle\Traits\Timestampable;
use AppBundle\Traits\UploadableSingle;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, AdvancedUserInterface, Serializable
{

    use Timestampable, UploadableSingle;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @Serializer\Exclude()
     *
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @var bool
     * @Serializer\Exclude()
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * @Serializer\Exclude()
     *
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @Serializer\Exclude()
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @Serializer\Exclude()
     *
     * @ORM\Column(name="password_valid_until", type="datetime", nullable=true)
     */
    private $passwordValidUntil;

    /**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\File", cascade={"persist"})
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $file;

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
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Has role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function getFullname() {
        return ucfirst($this->firstname) . ' ' . strtoupper($this->lastname);
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }


    /**
     * Set passwordValidUntil
     *
     * @param \DateTime $passwordValidUntil
     *
     * @return User
     */
    public function setPasswordValidUntil($passwordValidUntil)
    {
        $this->passwordValidUntil = $passwordValidUntil;

        return $this;
    }

    /**
     * Get passwordValidUntil
     *
     * @return \DateTime
     */
    public function getPasswordValidUntil()
    {
        return $this->passwordValidUntil;
    }

    //Override methods :

    public function getUsername()
    {
        return $this->email;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return count($this->roles) > 0 ? $this->roles : null;
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return $this->passwordValidUntil == null || ($this->passwordValidUntil >= new DateTime("now"));
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
            $this->enabled,
        ));
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
            $this->enabled
        ) = $data;
    }
}
