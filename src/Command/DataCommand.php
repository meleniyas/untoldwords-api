<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Home;

#[AsCommand(
    name: 'app:init',
    description: 'init manager.',
    hidden: false,
    aliases: ['app:init']
)]
class DataCommand extends Command
{
    private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:init')
            ->setDescription('Inicializa el manager');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $clear = $this->em->getRepository(User::class)->findOneBy([
            "rol" => 0,
        ]);

        if ($clear !== null) {
            $this->em->remove($clear);
            $this->em->flush();
        }

        $user = new User();
        $user->setEmail("elias@untoldworlds.com");
        $user->setName("Elias");
        $user->setLastName1("Tudela");
        $user->setLastName2("Martínez");
        $user->setRol("ADMIN");
        $user->setPassword(password_hash("holahola", PASSWORD_BCRYPT));

        $this->em->persist($user);
        $this->em->flush();


        $clear = $this->em->getRepository(Home::class)->findAll();

        if ($clear !== null) {
            foreach ($clear as $c) {
                $this->em->remove($c);
            }
            $this->em->flush();
        }

        $home = new Home();
        $home->setTimer(2); // segs
        $this->em->persist($home);

        $this->em->flush();



        $output->writeln('Manager initialized');

        return Command::SUCCESS;
    }
}
