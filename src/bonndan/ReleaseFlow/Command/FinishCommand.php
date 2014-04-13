<?php

namespace bonndan\ReleaseFlow\Command;

use bonndan\ReleaseFlow\Exception;
use bonndan\ReleaseFlow\Version;
use bonndan\ReleaseFlow\Version\Detector\GitFlowBranch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that eases releasing with "git flow".
 * 
 * This commands finishes a regular git flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class FinishCommand extends Command {

    use FlowDependenciesTrait;

    protected function configure() {
        $this
            ->setName('finish')
            ->setDescription('Finish a git flow release.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!$this->flow->isInTheFlow()) {
            throw new Exception('You are not in a git flow release branch (' . $this->vcs->getCurrentBranch(). ').');
        }

        $composerVersion = $this->composerFile->getCurrentVersion();
        $branchVersion = $this->flow->getCurrentVersion();
        if (!Version::gt($branchVersion, $composerVersion)) {
            throw new Exception('The git flow version is not greater than the composer version ' . $composerVersion->getVersion());
        }


        $branchType = $this->flow->getBranchType();
        if ($branchType != GitFlowBranch::RELEASE) {
            if ($this->getDialog()->askConfirmation($output, 'Please confirm to finish this hotfix (yes).')) {
                $publish = $this->getDialog()->askConfirmation($output, 'Shall this hotfix be published automatically (no)?', false);
                $this->updateComposerFile($branchVersion);
                $this->vcs->finishHotfix($publish);
            }
        } else {
            $difference = $composerVersion->getDifferenceType($branchVersion);
            if ($this->getDialog()->askConfirmation($output, 'Please confirm to finish this <info>' . $difference . '</info> release (yes).')) {
                $publish = $this->getDialog()->askConfirmation($output, 'Shall this release be published automatically (no)?', false);
                $this->updateComposerFile($branchVersion);
                $this->vcs->finishRelease($publish);
            }
        }
    }

    private function updateComposerFile(Version $branchVersion)
    {
        $this->composerFile->setVersion($branchVersion);
        $this->vcs->saveWorkingCopy('Bumped composer file version to ' . $branchVersion->getVersion());
    }
}
