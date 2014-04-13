<?php

namespace bonndan\ReleaseFlow\VCS;

use bonndan\ReleaseFlow\Exception;
use bonndan\ReleaseFlow\Version;
use bonndan\ReleaseFlow\Version\Detector\GitFlowBranch;
use PHPGit\Git as PHPGit;

/**
 * Git implementation of the VCSInterface
 * 
 * Is a bridge to PHPGit\Git.
 * 
 * @link https://github.com/kzykhys/PHPGit
 */
class Git implements VCSInterface
{
    /**
     * git
     * 
     * @var PHPGit 
     */
    private $git;
    
    /**
     * dry-run: do nothing
     * @var boolean
     */
    private $dryRun = false;
    
    private $dryRunCommandWords = array('tag', 'push', 'add', 'commit');
    
    /**
     * Factory method.
     * 
     * @param string $directory
     * @return Git
     */
    public static function create($directory)
    {
        $git = new PHPGit();
        $git->setRepository($directory);
        
        return new static($git);
    }
    
    /**
     * Constructor.
     * 
     * @param \PHPGit\Git $git
     */
    public function __construct(PHPGit $git)
    {
        $this->git = $git;
    }
    
    public function createTag(Version $tagName)
    {
        return $this->git->tag->create($tagName);
    }

    public function getCurrentBranch()
    {
        //quieted: see https://github.com/kzykhys/PHPGit/issues/4
        @$branches = $this->git->branch();
        foreach ($branches as $branch) {
            if (isset($branch['current']) && $branch['current'] === true) {
                return $branch['name'];
            }
        }
    }

    public function hasLocalModifications()
    {
        $indicators = array(
            \PHPGit\Command\StatusCommand::MODIFIED,
            \PHPGit\Command\StatusCommand::UNTRACKED,
        );
        
        $status =  $this->git->status();
        foreach ($status['changes'] as $mod) {
            if (in_array($mod['index'], $indicators)) {
                return true;
            }
        }
        
        return false;
    }

    public function getTags()
    {
        return $this->git->tag();
    }

    public function saveWorkingCopy($commitMsg = '')
    {
        return $this->git->commit($commitMsg);
    }

    /**
     * Returns the highest valid version tag.
     *
     * @return Version
     */
    public function getCurrentVersion()
    {
        $tags = $this->getValidVersionTags();
        if (count($tags) === 0) {
            return Version::createInitialVersion();
        }

        usort($tags, array("vierbergenlars\\SemVer\\version", "compare"));

        return new Version(array_pop($tags));
    }
    
    /**
     * Start a git flow release.
     * 
     * @param Version $version
     * @return array output
     */
    public function startRelease(Version $version)
    {
        $command = "flow release start " . $version;
        return $this->executeGitCommand($command);
    }
    
    /**
     * Finishes the current git flow release without tagging.
     * 
     * @return Version
     * @throws Exception
     */
    public function finishRelease()
    {
        $detector = new GitFlowBranch($this, GitFlowBranch::RELEASE);
        $version = $detector->getCurrentVersion();
        $command = 'flow release finish  -m "' . (string)$version . '" ' . (string)$version . ' 1>/dev/null 2>&1';
        $this->executeGitCommand($command);
        return $version;
    }
    
    /**
     * Start a git flow hotfix.
     * 
     * @param Version $version
     * @return array output
     */
    public function startHotfix(Version $version)
    {
        $command = "flow hotfix start " . $version;
        return $this->executeGitCommand($command);
    }
    
    /**
     * Finishes the current git flow hotfix without tagging.
     * 
     * @return Version
     * @throws Exception
     */
    public function finishHotfix()
    {
        $detector = new GitFlowBranch($this, GitFlowBranch::HOTFIX);
        $version = $detector->getCurrentVersion();
        $command = 'flow hotfix finish -m "' . (string)$version . '" ' . (string)$version . ' 1>/dev/null 2>&1';
        $this->executeGitCommand($command);
        return $version;
    }

    public function setDryRun($flag)
    {
        $this->dryRun = (bool)$flag;
    }
    
    /**
     * Return all tags matching the versionRegex and prefix
     *
     * @return Version[]
     */
    private function getValidVersionTags()
    {
        $valid = $this->filtrateList($this->getTags());

        $versions = array();
        foreach ($valid as $versionNumber) {
            $versions[] = new Version($versionNumber);
        }

        return $versions;
    }
    
    /**
     * Remove all invalid tags from a list
     * 
     * @param string[]
     * @return string[]
     */
    protected function filtrateList($tags)
    {
        $validTags = array();
        foreach ($tags as $tag) {
            if (Version::isValid($tag)) {
                $validTags[] = $tag;
            }
        }
        return $validTags;
    }
    
    /**
     * Executes a git command.
     * 
     * @param string $cmd
     * @return mixed
     * @throws Exception
     */
    private function executeGitCommand($cmd)
    {
        /**
         * @link http://stackoverflow.com/a/10986987
         * @link https://github.com/symfony/symfony/pull/3565 
         * @link https://github.com/symfony/symfony/issues/3555
        $builder = $this->git->getProcessBuilder();
        $builder->inheritEnvironmentVariables(true); //
        $builder->add($cmd);
        $process = $builder->getProcess();
         * 
         */
        
        if ($this->dryRun) {
            return $cmd;
        }
        
        $var = null;
        system('git ' . $cmd, $var);
        return $var;
        //return $this->git->run($process);
    }
}
