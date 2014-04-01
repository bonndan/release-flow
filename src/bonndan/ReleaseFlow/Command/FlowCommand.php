<?php
namespace bonndan\ReleaseFlow\Command;

use bonndan\ReleaseFlow\VCS\VCSInterface;
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

    public function __construct(GitFlowBranch $flow, VCSInterface $vcs, $name = null)
    {
        parent::__construct($name);
        $this->flow = $flow;
        $this->vcs = $vcs;
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
