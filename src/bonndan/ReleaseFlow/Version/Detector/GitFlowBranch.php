<?php

namespace bonndan\ReleaseFlow\Version\Detector;

use bonndan\ReleaseFlow\Exception;
use bonndan\ReleaseFlow\VCS\VCSInterface;
use bonndan\ReleaseFlow\Version;
use vierbergenlars\SemVer\SemVerException;

/**
 * Detects the "current" version of a git flow branch.
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class GitFlowBranch implements DetectorInterface
{
    /**
     * release identifying string in branch name
     * @var string
     */
    const RELEASE = 'release';
    
    /**
     * hotfix identifying string in branch name
     * @var string
     */
    const HOTFIX = 'hotfix';
    
    /**
     * vcs
     * 
     * @var VCSInterface 
     */
    private $vcs;

    /**
     * Branch type (release|hotfix)
     * 
     * @var string
     */
    private $branchType;
    
    /**
     * Constructor.
     * 
     * @param VCSInterface    $vcs
     * @param string $branchType limit detection to a branch type
     */
    public function __construct(VCSInterface $vcs, $branchType = null)
    {
        $this->vcs = $vcs;
        $this->branchType = $branchType;
    }

    /**
     * Detects the current version based on branch name.
     * 
     * @return Version
     * @throws Exception
     */
    public function getCurrentVersion()
    {
        /*
         * exception is not caught if a branch type is defined. 
         */
        if ($this->branchType !== null) {
            return $this->detect($this->branchType);
        }
        
        try {
            $version = $this->detect(self::RELEASE);
            $this->branchType = self::RELEASE;
            return $version;
        } catch (Exception $ex) {

        }
        
        try {
            $version = $this->detect(self::HOTFIX);
            $this->branchType = self::HOTFIX;
            return $version;
        } catch (Exception $ex) {

        }
        
        throw new Exception('Cannot detect release or hotfix branch.');
    }
    
    /**
     * Detects a version based on branch type.
     * 
     * @param string $branchType
     * @return Version
     * @throws Exception
     */
    private function detect($branchType)
    {
        $branch = $this->vcs->getCurrentBranch();
        if (strpos($branch, $branchType . '/') !== 0) {
            throw new Exception('Expected to find "' . $branchType . '/" at beginning of branch name.');
        }
        
        try {
            $version = new Version(str_replace($branchType . "/", "", $branch));
        } catch (SemVerException $ex) {
            throw new Exception('Cannot detect version in branch name: ' . $ex->getMessage());
        }
        
        return $version;
    }

    /**
     * Checks if the current branch is a release or hotfix branch.
     * 
     * @return boolean
     */
    public function isInTheFlow()
    {
        $branch = $this->vcs->getCurrentBranch();
        return (strpos($branch, self::RELEASE . '/') === 0 || strpos($branch, self::HOTFIX . '/') === 0);
    }
    
    /**
     * Returns the set or autodetected branch type.
     * 
     * @return string
     */
    public function getBranchType()
    {
        return $this->branchType;
    }
}
