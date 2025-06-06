<?php
// src/Command/CreateAdminCommand.php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un compte admin pour vous connecter à /admin'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = 'admin@example.com';
        $plain = 'admin123';

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setFirstName('Super');
        $user->setLastName('Admin');
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setPassword($this->hasher->hashPassword($user, $plain));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("<info>Admin créé :</info> {$email} / {$plain}");
        return Command::SUCCESS;
    }
}
