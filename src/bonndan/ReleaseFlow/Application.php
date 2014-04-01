<?php

namespace bonndan\ReleaseFlow;

use bonndan\ReleaseFlow\Command\StartCommand;
use bonndan\ReleaseFlow\VCS\Git as Git2;
use bonndan\ReleaseFlow\VCS\VCSInterface;
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

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->ensureDependencies();
        $this->addCommand('bonndan\ReleaseFlow\Command\StartCommand');
        
        parent::run($input, $output);
    }

    /**
     * Inject a vcs implementation.
     * 
     * @param \bonndan\ReleaseFlow\VCS\VCSInterface $vcs
     */
    public function setVcs(VCSInterface $vcs)
    {
        $this->vcs = $vcs;
    }

    /**
     * Creates and adds a new command.
     * 
     * @param string $name command class name
     */
    private function addCommand($name)
    {
        $this->add(new $name($this->flow, $this->vcs));
    }
    
    /**
     * Ensures that all dependencies are present.
     * 
     * If not injected previously, default instances are created. The commands
     * need all the depedencies.
     */
    private function ensureDependencies()
    {
        if ($this->vcs === null) {
            $git = new Git();
            $git->setRepository(getcwd());

            $this->vcs = new Git2($git);
        }
        
        if ($this->flow === null) {
            $this->flow = new GitFlowBranch($this->vcs);
        }
    }
}
