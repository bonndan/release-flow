<?php
namespace bonndan\ReleaseFlow\Command;

use bonndan\ReleaseFlow\VCS\VCSInterface;
use bonndan\ReleaseFlow\Version\ComposerFile;
use bonndan\ReleaseFlow\Version\Detector\GitFlowBranch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * Base command
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
abstract class FlowCommand extends Command
{
    /**
     * The git flow branch version detector.
     * 
     * @var GitFlowBranch
     */
    protected $flow;
    
    /**
     * The version control
     * 
     * @var VCSInterface
     */
    protected $vcs;
    
    /**
     * The composer file, optional
     * 
     * @var ComposerFile|null
     */
    protected $composerFile;

    /**
     * Constructor.
     * 
     * @param GitFlowBranch $flow
     * @param VCSInterface $vcs
     * @param ComposerFile $composerFile
     * @param string $name
     */
    final public function __construct(GitFlowBranch $flow, VCSInterface $vcs, ComposerFile $composerFile = null, $name = null)
    {
        parent::__construct($name);
        $this->flow = $flow;
        $this->vcs = $vcs;
        $this->composerFile = $composerFile;
    }
    
    /**
     * Returns the dialog helper.
     * 
     * @return DialogHelper
     */
    protected function getDialog()
    {
        return $this->getHelperSet()->get('dialog');
    }

}
