<?php

namespace Nakato53\Vercel\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

#[AsCommand('vercel:server')]
class VercelServerCommand extends Command
{

    public function __construct(private string $rootDirectory)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate Serverside API for Vercel Lambda Runtime')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();

        
        try {
            $filesystem->symlink($this->rootDirectory.'/public',$this->rootDirectory . '/api', true);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }


        $io->success('Creating API for Vercel Lambda Runtime : OK');

        return 1;
    }
}
