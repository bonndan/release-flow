<?php

namespace bonndan\ReleaseFlow\Tests\Unit\Version;

use bonndan\ReleaseFlow\VCS\Git;

class GitTest extends \PHPUnit_Framework_TestCase
{
    protected $testDir;

    protected function setUp()
    {
        // Create a temp folder and extract inside the git test folder
        $tempDir = tempnam(sys_get_temp_dir(),'');
        if (file_exists($tempDir)) {
            unlink($tempDir);
        }
        mkdir($tempDir);
        chdir($tempDir);
        exec('unzip '.__DIR__.'/gitRepo.zip');
        exec('git checkout .');
        $this->testDir = $tempDir;
    }

    public function testHasLocalModifications()
    {
        $vcs = Git::create($this->testDir);
        exec('touch foo');
        $modifs = $vcs->hasLocalModifications();
        $this->assertTrue($modifs);
    }

    public function testGetTags()
    {
        $vcs = Git::create($this->testDir);
        $this->assertEquals(array("1.0.0","1.1.0"), $vcs->getTags());
    }
    
    public function testGetCurrentVersion()
    {
        $vcs = Git::create($this->testDir);
        $this->assertEquals("1.1.0", $vcs->getCurrentVersion());
    }

    public function testCreateTag()
    {
        $vcs = Git::create($this->testDir);
        $vcs->createTag(new \bonndan\ReleaseFlow\Version('2.0.0'));
        $this->assertEquals(array("1.0.0","1.1.0","2.0.0"), $vcs->getTags());
    }

    public function testSaveWorkingCopy()
    {
        $vcs = Git::create($this->testDir);
        system("touch foo");
        system("git add foo");
        $vcs->saveWorkingCopy('test');
    }

    public function testGetCurrentBranch()
    {
        $vcs = Git::create($this->testDir);
        $this->assertEquals('master', $vcs->getCurrentBranch());
        system("git checkout -b foo --quiet");
        $this->assertEquals('foo', $vcs->getCurrentBranch());
        exec("git checkout master --quiet");
        $this->assertEquals('master', $vcs->getCurrentBranch());
    }

    public function testGetCurrentBranchWhenNotInBranch()
    {
        $vcs = Git::create($this->testDir);
        exec("git checkout 9aca70b --quiet");
        $vcs->getCurrentBranch();
    }
    
    public function testStartRelease()
    {
        $version = new \bonndan\ReleaseFlow\Version('2.2.2');
        system("git flow init -fd 1>/dev/null 2>&1");
        $vcs = Git::create($this->testDir);
        $vcs->setDryRun(true);
        $cmd = $vcs->startRelease($version);
        $this->assertEquals('flow release start 2.2.2', $cmd);
    }
    
    public function testFinishRelease()
    {
        $vcs = Git::create($this->testDir);
        
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow release start 2.2.2 1>/dev/null 2>&1");
        $vcs->setDryRun(true);
        $version = $vcs->finishRelease();
        $this->assertEquals('2.2.2', $version->getVersion());
    }
    
    public function testFinishReleaseException()
    {
        $vcs = Git::create($this->testDir);
        system("git flow init -fd 1>/dev/null 2>&1");
        
        $this->setExpectedException("\bonndan\ReleaseFlow\Exception", "Expected to find");
        $vcs->finishRelease();
    }

    protected function tearDown()
    {
        // Remove the test folder
        exec('rm -rf '.$this->testDir);
        chdir(__DIR__);
    }

}
