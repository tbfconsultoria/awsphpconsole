<?php

namespace App\Command\Aws\Ec2;

//use Aws\Credentials\CredentialProvider;
use Aws\Ec2\Ec2Client;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class StartCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:aws-ec2:start')
            // the short description shown while running "php bin/console list"
            ->setDescription('Inicia uma instancia.')
            ->addArgument('instanceId', InputArgument::IS_ARRAY, 'Qual instancia iniciar?');
    }

    protected function execute(InputInterface $input,
                               OutputInterface $output)
    {
        $instanceId = $input->getArgument('instanceId');
        $io = new SymfonyStyle($input, $output);
        $io->title('Start Ec2 Instances');
        $myProvider = new \App\Aws\CredentialsProvider();
        $client = new Ec2Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => $myProvider->getProvider()
        ]);
        try {
            $result = $result = $client->startInstances([
                'InstanceIds' => $instanceId
            ]);
            foreach ($result['StartingInstances'] as $instance) {
                $io->writeln($instance['InstanceId'] . ' - ' . $instance['CurrentState']['Name']);
            }
        } catch (\Exception $e) {
            $io->error("verifique o ID da instancia\n" . implode("\n", $instanceId));
            $output->writeln("#ERRO " . $e->getMessage());
        }
    }
}