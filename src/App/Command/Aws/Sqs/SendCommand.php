<?php
/**
 * Created by PhpStorm.
 * User: ideal
 * Date: 08/11/16
 * Time: 15:49
 */

namespace App\Command\Aws\Sqs;

use Aws\Sqs\SqsClient;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:aws-sqs:send')
            // the short description shown while running "php bin/console list"
            ->setDescription('Envia uma nova mensagem para a fila.')
            ->addArgument('queueName', null, 'Qual o nome da fila?')
            ->addArgument('message', null, 'Mensagem que deseja gravar');
    }

    protected function execute(InputInterface $input,
                               OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $client = new SqsClient([
            'profile' => 'tbf',
            'region' => 'us-east-1',
            'version' => 'latest'
        ]);

        $result = $client->createQueue(array('QueueName' => $input->getArgument('queueName')));
        $queueUrl = $result->get('QueueUrl');
        echo $queueUrl;
        $r = $client->sendMessage(['QueueUrl' => $queueUrl,
            'MessageBody' => $input->getArgument('message')]);
        $io->success($r->get('MessageId'));
    }
}