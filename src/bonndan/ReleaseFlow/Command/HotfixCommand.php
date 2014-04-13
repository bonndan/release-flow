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
 * This commands creates a hotfix branch with a patch version increment.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class HotfixCommand extends Command
{
    use FlowDependenciesTrait;
    
    protected function configure()
    {
        $this
            ->setName('hotfix')
            ->setDescription('Begin a git flow hotfix.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->flow->isInTheFlow()) {
            throw new Exception('You are already in a git flow branch.');
        }

        $currentVersion = $this->vcs->getCurrentVersion();
        $nextVersion    = $currentVersion->inc(Version::TYPE_PATCH);
        
        $output->writeln('<info>The next version will be ' . $nextVersion . '.</info>');
        $this->vcs->startHotfix($nextVersion);
    }
}
