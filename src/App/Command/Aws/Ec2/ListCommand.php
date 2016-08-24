<?php

namespace App\Command\Aws\Ec2;

//use Aws\Credentials\CredentialProvider;
use Aws\Ec2\Ec2Client;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class ListCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:aws-ec2:list')
            // the short description shown while running "php bin/console list"
            ->setDescription('AC/DC - Hells Bells.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("You got me ringing hells bells...")
//            ->addArgument('argument', InputArgument::REQUIRED, 'Who do you want to greet?')
//            ->setDefinition(
//                new InputDefinition(array(
//                    new InputOption('foo', 'f', InputOption::VALUE_REQUIRED, 'A foo option'),
//                    new InputOption('bar', 'b', InputOption::VALUE_REQUIRED),
//                    new InputOption('cat', 'c', InputOption::VALUE_OPTIONAL),
//                ))
//            )
        ;
    }

    protected function execute(InputInterface $input,
                               OutputInterface $output)
    {
//        $args = $input->getArguments();
//        $options = $input->getOption('foo');
        $io = new SymfonyStyle($input, $output);
        $io->title('Lis all Ec2 Instances');
        $myProvider = new \App\Aws\CredentialsProvider();
        $client = new Ec2Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => $myProvider->getProvider()
        ]);
        try {
            $result = $client->describeInstances();
            $reservations = $result->get('Reservations');
            $table = [];
            foreach ($reservations as $i => $reservation) {
                $table[$i][] = $i + 1;
                $table[$i]['id'] = $reservation['ReservationId'];
                foreach ($reservation['Instances'] as $instance) {
                    $table[$i]['State'] = $instance['State']['Name'];
                    $table[$i]['name'] = $instance['Tags'][0]['Value'];
                    $table[$i]['PublicDnsName'] = $instance['PublicDnsName'];
                }
            }
            $io->table(
                array('#', '#id', 'status', 'Name', 'DNS'),
                $table
            );
        } catch (Exception $e) {
            $output->writeln("#ERRO " . $e->getMessage());
        }
    }
}