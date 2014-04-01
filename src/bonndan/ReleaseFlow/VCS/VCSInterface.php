<?php

namespace bonndan\ReleaseFlow\VCS;

use bonndan\ReleaseFlow\Exception;
use bonndan\ReleaseFlow\Version;

/**
 * Interface for version control systems.
 * 
 * 
 */
interface VCSInterface
{
    /**
     * Return the current branch
     */
    public function getCurrentBranch();

    /**
     * Returns the hight valid version.
     *
     * @return Version
     */
    public function getCurrentVersion();

    /**
     * Return all tags of the project
     *
     * @return array
     */
    public function getTags();

    /**
     * Create a new tag at the current position
     *
     * @param Version $tagName
     */
    public function createTag(Version $tagName);

    /**
     * Returns if if local modifications exist.
     * 
     * @return boolean
     */
    public function hasLocalModifications();

    /**
     * Save the local modifications (commit)
     * 
     * @param $commitMsg
     * @return mixed
     */
    public function saveWorkingCopy($commitMsg = '');
    
    /**
     * Start a git flow release.
     * 
     * @param Version $version
     * @return array output
     */
    public function startRelease(Version $version);
    
    /**
     * Finishes the current git flow release without tagging.
     * 
     * @return Version
     * @throws Exception
     */
    public function finishRelease();
    
    /**
     * Start a git flow hotfix.
     * 
     * @param Version $version
     * @return array output
     */
    public function startHotfix(Version $version);
    
    /**
     * Finishes the current git flow hotfix without tagging.
     * 
     * @return Version
     * @throws Exception
     */
    public function finishHotfix();
}
