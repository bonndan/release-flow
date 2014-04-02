<?php
namespace bonndan\ReleaseFlow\Command;

use bonndan\ReleaseFlow\Version;
use Symfony\Component\Console\Command\ListCommand as SFListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Overwrites the default list command.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class ListCommand extends SFListCommand
{
    use FlowDependenciesTrait;
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkVersions($output);
        parent::execute($input, $output);
    }
    
    /**
     * Displays a warning if the composer and vcs versions are out of sync.
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return type
     */
    public function checkVersions(OutputInterface $output)
    {
        $composerVersion = $this->composerFile->getCurrentVersion();
        $vcsVersion = $this->vcs->getCurrentVersion();
        
        if (!$composerVersion || !$vcsVersion) {
            return;
        }
        
        if (!Version::eq($vcsVersion, $composerVersion)) {
            $output->writeln('<error>The composer file version ' . $composerVersion->getVersion() 
                    . ' is not the same as the latest VCS version ' . $vcsVersion->getVersion() .'.</error>');
            $output->writeln('You should set the composer version manually.');
        }
    }
}
