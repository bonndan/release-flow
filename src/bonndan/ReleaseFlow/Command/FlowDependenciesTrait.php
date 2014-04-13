<?php
namespace bonndan\ReleaseFlow\Command;

use bonndan\ReleaseFlow\VCS\VCSInterface;
use bonndan\ReleaseFlow\Version\ComposerFile;
use bonndan\ReleaseFlow\Version\Detector\GitFlowBranch;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * Base command
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
trait FlowDependenciesTrait
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
     */
    public function setReleaseFlowDependencies(GitFlowBranch $flow, VCSInterface $vcs, ComposerFile $composerFile = null)
    {
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
