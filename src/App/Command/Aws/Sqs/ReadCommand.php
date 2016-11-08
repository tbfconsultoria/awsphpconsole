<?php


namespace App\Command\Aws\Sqs;

//use Aws\Credentials\CredentialProvider;
use Aws\Sqs\SqsClient;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReadCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:aws-sqs:read')
            // the short description shown while running "php bin/console list"
            ->setDescription('Le uma fila.')
            ->addArgument('queueName', null, 'Qual o nome da fila?')
            ->addArgument('maxNumberOfMessages', null, 'Número máximo de mensagens', 10)
            ->addArgument('i', null, 'Iterações', 5);
    }

    protected function execute(InputInterface $input,
                               OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln("TESTE SQS LEITURA #" . $input->getArgument('queueName'));
        $client = new SqsClient([
            'profile' => 'tbf',
            'region' => 'us-east-1',
            'version' => 'latest'
        ]);

        $maxNumberOfMessages = $input->getArgument('maxNumberOfMessages');
        $iteracoes = $input->getArgument('i');
        $result = $client->createQueue(array('QueueName' => $input->getArgument('queueName')));
        $queueUrl = $result->get('QueueUrl');

        $i = 0;
        while (true) {
            $i++;
            $io->writeln("BUSCA ... #" . $i . " ->" . $input->getArgument('queueName') . " ");
            $io->progressStart(100);
            $result = $client->receiveMessage(array(
                'QueueUrl' => $queueUrl,
                'MaxNumberOfMessages' => $maxNumberOfMessages,
                'MessageAttributeNames' => [
                    ".*"
                ]
            ));
//    print_r($result);
            $io->progressFinish();
            if (is_array($result->get('Messages'))) {
                foreach ($result->get('Messages') as $message) {
                    // Do something with the message
                    $io->success($message['MessageId']);
                    $io->section($message['Body']);
//                    if (isset($message['MessageAttributes'])) {
//                        foreach ($message['MessageAttributes'] as $attribute => $value) {
//                            echo PHP_EOL . "\t" . $attribute . "\t=>" . $value['StringValue'];
//                        }
//                    }
                }
            } else {
                $io->writeln("sem mensagens...");
            }
            echo PHP_EOL;
            if ($i == $iteracoes)
                break;
            sleep(2);
        }
    }
}