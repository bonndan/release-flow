<?php

namespace bonndan\ReleaseFlow\Command;

use bonndan\ReleaseFlow\Exception;
use bonndan\ReleaseFlow\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that eases releasing with "git flow".
 * 
 * This commands asks for the version increment and then creates a regular git 
 * flow release branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class StartCommand extends Command
{
    use FlowDependenciesTrait;
    
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('Begin a git flow release.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->flow->isInTheFlow()) {
            throw new Exception('You are already in a git flow branch.');
        }

        $dialog = $this->getDialog();
        $increments = array(
            Version::TYPE_MAJOR => '1.x.y => 2.0.0 (MUST on backwards <comment>incompatible</comment> changes)',
            Version::TYPE_MINOR => '1.2.x => 1.3.0 (MUST on new backwards compatible functionality or public functionality marked as deprecated. MAY on substantial new functionality or improvements)',
            Version::TYPE_PATCH => '1.2.3 => 1.2.4 (MUST if only backwards compatible bug fixes introduced)',
        );
        $inc = $dialog->select(
            $output, '<question>Please enter the version increment for your next release:</question>', $increments, 2
        );
        
        $currentVersion = $this->vcs->getCurrentVersion();
        $nextVersion    = $currentVersion->inc($inc);
        
        $output->writeln('<info>The next version will be ' . $nextVersion . '.</info>');
        $this->vcs->startRelease($nextVersion);
    }

}
