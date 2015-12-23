<?php

namespace VG\GuestbookBundle\Command;

use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//php app/console guestbook:clear

class DeleteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('guestbook:clear')
            ->setDescription('Delete old N messages')
            ->addArgument(
                'N',
                InputArgument::REQUIRED,
                'count to delete'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $n = (int)$input->getArgument('N');

        // удаляем "прямым" запросом, т.к. может быть много записей к удалению и через доктрину удалять будет дольше.
        $sql = "
        DELETE FROM message ORDER BY id ASC LIMIT " . $n . "
    ";
        $stmt = $this->getContainer()->get('doctrine')->getManager()->getConnection()->prepare($sql);
        /* @var $stmt Statement */
        if ($stmt->execute()) {
            $text = 'deleted successful!';
        } else {
            $text = 'error';
        };
        $output->writeln($text);
    }
}