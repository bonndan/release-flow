<?php
namespace bonndan\ReleaseFlow\Command;

use bonndan\ReleaseFlow\Exception;
use bonndan\ReleaseFlow\Version;
use bonndan\ReleaseFlow\Version\Detector\GitFlowBranch;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that eases releasing with "git flow".
 * 
 * This commands finishes a regular git flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class FinishCommand extends FlowCommand
{
    
    protected function configure()
    {
        $this
            ->setName('finish')
            ->setDescription('Finish a git flow release.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->flow->isInTheFlow() || $this->flow->getBranchType() != GitFlowBranch::RELEASE) {
           throw new Exception('You are not in a git flow release branch.');
        }

        $composerVersion = $this->composerFile->getCurrentVersion();
        $branchVersion = $this->flow->getCurrentVersion();
        if (!Version::gt($branchVersion, $composerVersion)) {
            throw new Exception('The git flow version is not greater than the composer version ' . $composerVersion->getVersion());
        }
        
        $type = $composerVersion->getDifferenceType($branchVersion);
        
        if ($this->getDialog()->askConfirmation($output, 'Please confirm to finish this <info>' . $type . '</info> release.')) {
            $this->vcs->finishRelease();
        }
    }

}