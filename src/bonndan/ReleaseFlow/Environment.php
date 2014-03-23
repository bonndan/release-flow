<?php
namespace bonndan\ReleaseFlow;

use bonndan\ReleaseFlow\VCS\VCSInterface;

/**
 * Provides information on the environment.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Environment
{
    /**
     * vcs
     * 
     * @var VCSInterface
     */
    private $vcs;
    
    /**
     * Constructor.
     * 
     * @param \bonndan\ReleaseFlow\VCS\VCSInterface $vcs
     */
    public function __construct(VCSInterface $vcs)
    {
        $this->vcs = $vcs;
    }
    
    public function getConfiguredVersion()
    {
        
    }
    
    public function getVCSVersion()
    {
        
    }
    
    public function getFlowVersion()
    {
        
    }
}
