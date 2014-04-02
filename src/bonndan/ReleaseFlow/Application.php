<?php

namespace bonndan\ReleaseFlow;

use bonndan\ReleaseFlow\VCS\Git as Git2;
use bonndan\ReleaseFlow\VCS\VCSInterface;
use bonndan\ReleaseFlow\Version\ComposerFile;
use bonndan\ReleaseFlow\Version\Detector\GitFlowBranch;
use PHPGit\Git;
use Symfony\Component\Console\Application as SFApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The console application.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Application extends SFApplication
{
    /**
     * vcs
     * 
     * @var VCSInterface
     */
    private $vcs;
    
    /**
     * git flow branch version detector.
     * 
     * @var GitFlowBranch
     */
    private $flow;
    
    /**
     * composer file
     * 
     * @var ComposerFile
     */
    private $composerFile;

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->ensureDependencies();
        $this->addCommand('bonndan\ReleaseFlow\Command\StartCommand');
        $this->addCommand('bonndan\ReleaseFlow\Command\FinishCommand');
        $this->addCommand('bonndan\ReleaseFlow\Command\ListCommand');
        
        parent::run($input, $output);
    }

    /**
     * Inject a vcs implementation.
     * 
     * @param VCSInterface $vcs
     */
    public function setVcs(VCSInterface $vcs)
    {
        $this->vcs = $vcs;
    }

    /**
     * Inject a composer file.
     * 
     * @param ComposerFile $composerFile
     */
    public function setComposerFile(ComposerFile $composerFile)
    {
        $this->composerFile = $composerFile;
    }
    
    /**
     * Creates and adds a new command.
     * 
     * @param string $name command class name
     */
    private function addCommand($name)
    {
        $this->add(new $name($this->flow, $this->vcs, $this->composerFile));
    }
    
    /**
     * Ensures that all dependencies are present.
     * 
     * If not injected previously, default instances are created. The commands
     * need all the depedencies.
     */
    private function ensureDependencies()
    {
        $workingDir = getcwd();
        if ($this->vcs === null) {
            $git = new Git();
            $git->setRepository($workingDir);

            $this->vcs = new Git2($git);
        }
        
        if ($this->flow === null) {
            $this->flow = new GitFlowBranch($this->vcs);
        }
        
        if ($this->composerFile === null) {
            $file = new \SplFileObject($workingDir . '/composer.json');
            $this->composerFile = new ComposerFile($file);
        }
    }
}
