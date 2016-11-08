<?php


namespace App\Command\Aws\Sqs;

//use Aws\Credentials\CredentialProvider;
use Aws\Sqs\SqsClient;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:aws-sqs:read')
            // the short description shown while running "php bin/console list"
            ->setDescription('Le uma fila.')
            ->addArgument('queueName', null, 'Qual o nome da fila?');
    }

    protected function execute(InputInterface $input,
                               OutputInterface $output)
    {
        echo "TESTE SQS LEITURA #" . $input->getArgument('queueName') . PHP_EOL;
        $client = new SqsClient([
            'profile' => 'tbf',
            'region' => 'us-east-1',
            'version' => 'latest'
        ]);

        $result = $client->createQueue(array('QueueName' => $input->getArgument('queueName')));
        $queueUrl = $result->get('QueueUrl');

        while (true) {
            echo "BUSCA ..." . $input->getArgument('queueName') . " ";
            $result = $client->receiveMessage(array(
                'QueueUrl' => $queueUrl,
                'MaxNumberOfMessages' => 10,
                'MessageAttributeNames' => [
                    ".*"
                ]
            ));
//    print_r($result);
            if (is_array($result->get('Messages'))) {
                foreach ($result->get('Messages') as $message) {
                    // Do something with the message
                    echo PHP_EOL . $message['MessageId'];
                    echo PHP_EOL . "" . $message['Body'];
                    foreach ($message['MessageAttributes'] as $attribute => $value) {
                        echo PHP_EOL . "\t" . $attribute . "\t=>" . $value['StringValue'];
                    }
                }
            } else {
                echo "sem mensagens...";
            }
            sleep(5);
            echo PHP_EOL;
        }
    }
}