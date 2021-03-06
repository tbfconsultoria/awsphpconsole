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
            ->setDescription('Lista instancias.')
            ->setHelp("")
        ;
    }

    protected function execute(InputInterface $input,
                               OutputInterface $output)
    {
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
                foreach ($reservation['Instances'] as $instance) {
                    $table[$i]['id'] = $instance['InstanceId'];
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