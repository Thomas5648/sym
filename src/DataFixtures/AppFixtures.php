<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Jednostka;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private const USERS = [
        [
            'username' => 'tom_pal',
            'email' => 'tom@wp.com',
            'password' => 'tom123',
            'fullName' => 'Tomasz Palk',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'jan_now',
            'email' => 'jan@now.com',
            'password' => 'jan123',
            'fullName' => 'Jan Nowak',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'super_admin',
            'email' => 'super_admin@gold.com',
            'password' => 'admin12345',
            'fullName' => 'Cms Admin',
            'roles' => [User::ROLE_ADMIN]
        ],
    ];

private const POST_TEXT = [
        'Pierwszy post',
        'Drugi post',
        'Trzeci post',
        'Czwarty post',
        'Piąty post',
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
       $this->loadUser($manager);
       $this->loadCms($manager);
    }

    private function loadCms(ObjectManager $manager)
    {
        for ($i = 0; $i < 30; $i++)
        {
            $cms = new Jednostka();
            $cms->setText(
                self::POST_TEXT[rand(0,count(self::POST_TEXT)-1)]
        );
            $date = new \DateTime();
            $date->modify('-'. rand(0,10).'day');
            $cms->setTime($date);
            $cms->setUser($this->getReference(
                self::USERS[rand(0,count(self::USERS)-1)]['username']));

            $manager->persist($cms);
        }
        $manager->flush();
    }

    private function loadUser(ObjectManager $manager)
    {
        foreach (self::USERS as $userData)
        {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setFullName($userData['fullName']);
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']));
    
            $user->setRoles($userData['roles']);
            $this->addReference($userData['username'], $user);
    
            $manager->persist($user);
        }
       
        $manager->flush();
    }
}