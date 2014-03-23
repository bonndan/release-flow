<?php

namespace bonndan\ReleaseFlow\VCS;

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
     * @return bonndan\ReleaseFlow\Version
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
}
