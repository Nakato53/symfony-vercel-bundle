<?php

namespace Nakato53\Vercel\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

#[AsCommand('vercel:install')]
class VercelInstallCommand extends Command
{

    private const PHPVercelMapping = [
        "7.4"=>"0.3.5",
        "8.0"=>"0.4.3",
        "8.1"=>"0.5.4",
        "8.2"=>"0.6.1"
    ];

    public function __construct(private string $rootDirectory)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setDescription('Install Vercel Runtime')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $phpVersions = array_keys(VercelInstallCommand::PHPVercelMapping);
        $phpChoices = [];
        foreach ($phpVersions as $phpVersion) {
            $phpChoices[$phpVersion] = $phpVersion;
        }
        $defaultPHPVersion = "8.2";
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select PHP version ( default is '.$defaultPHPVersion.' )',
            $phpChoices,
            $defaultPHPVersion
        );
        $question->setErrorMessage('PHP %s is invalid.');
        
        $phpversion = $helper->ask($input, $output, $question);
        $io->writeln('You have just selected: '.$phpversion);
        $io->writeln(['','']);
        $io->writeln('Generate vercel.json file');

        $vercelJsonEncoded = json_encode([
        
            "functions"=> [
              "api/*.php"=> [
                "runtime"=> "vercel-php@".VercelInstallCommand::PHPVercelMapping[$phpversion]
                ]
            ],
            "env"=>[
                "APP_ENV"=>"prod",
            ],
            "routes"=> [
                [ 
                    "src"=> "/(.*)",
                    "dest"=> "/api/index.php" 
                ]
            ]
        ],JSON_PRETTY_PRINT);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->rootDirectory.'/vercel.json', $vercelJsonEncoded);

        $io->success('Generate vercel.json file successfully for PHP '.$phpversion);

        
        if($filesystem->exists($this->rootDirectory. '/api')){
            $io->error('A /api already exist, Vercel require a /api directory, please remove it');
        }else{
            try {
                $filesystem->mkdir($this->rootDirectory . '/api');
                $filesystem->copy($this->rootDirectory.'/public/index.php',$this->rootDirectory . '/api/index.php', true);
                $filesystem->copy(__DIR__ . '/../../tocopy/assets.php',$this->rootDirectory . '/api/assets.php', true);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at ".$exception->getPath();
            }
        }


        $io->success('Creating API for Vercel Lambda Runtime : OK');
        
        
        $io->writeln(['','','Update composer.json file']);
        $composerJson = json_decode(file_get_contents($this->rootDirectory.'/composer.json'),true);

        if(isset($composerJson["scripts"]) && isset($composerJson["scripts"]["vercel"])){
            $io->warning([
                'Looks like vercel scripts already exist in composer.json file',
                'please check "@php bin/console vercel:server --env=prod" is set',
                'Add any script you have to run during vercel deployment'
                ]);
        }else{
            $composerJson["scripts"]["vercel"]=[
                        "@php bin/console cache:clear --env=prod",
                        "@php bin/console assets:install public --env=prod"
            ];
            $composerJsonEncoded = json_encode($composerJson,JSON_PRETTY_PRINT);
            $filesystem->dumpFile($this->rootDirectory.'/composer.json', $composerJsonEncoded);
            $io->success(['Updated composer.json with vercel scripts','Add any script you have to run during vercel deployment']);
        }
        

        
        
        $io->writeln(['','','You can now set your env variable inside vercel.json file']);
        $io->warning('Avoid any sensible data inside vercel.json file, use vercel setting on website to set private env variable');

        return 1;
    }
}
