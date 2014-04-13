<?php

namespace bonndan\ReleaseFlow\Tests\Version\Detector;

use bonndan\ReleaseFlow\VCS\Git;
use bonndan\ReleaseFlow\Version\Detector\GitFlowBranch;
use PHPUnit_Framework_TestCase;

/**
 * Description of GitFlowBranchTest
 *
 * @author daniel
 */
class GitFlowBranchTest extends PHPUnit_Framework_TestCase
{
    private $vcs;

    /**
     * system under test
     * @var GitFlowBranch 
     */
    protected $detector;

    protected function setUp()
    {
        $this->vcs = $this->getMock("\bonndan\ReleaseFlow\VCS\VCSInterface");
        $this->detector = new GitFlowBranch($this->vcs, GitFlowBranch::RELEASE);
    }

    public function testGetCurrentReleaseVersion()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentBranch')
                ->will($this->returnValue('release/2.2.2'));
        
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
    }
    
    public function testGetCurrentHotfixVersion()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentBranch')
                ->will($this->returnValue('hotfix/2.2.2'));
        
        $this->detector = new GitFlowBranch($this->vcs, GitFlowBranch::HOTFIX);
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
    }
    
    public function testGetCurrentHotfixVersionWithoutDefinedBranchType()
    {
        $this->vcs->expects($this->any())
                ->method('getCurrentBranch')
                ->will($this->returnValue('hotfix/2.2.2'));
        
        $this->detector = new GitFlowBranch($this->vcs);
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
        $this->assertEquals(GitFlowBranch::HOTFIX, $this->detector->getBranchType());
    }
    
    public function testGetCurrentReleaseVersionWithoutDefinedBranchType()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentBranch')
                ->will($this->returnValue('release/2.2.2'));
        
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
        $this->assertEquals(GitFlowBranch::RELEASE, $this->detector->getBranchType());
    }
    
    public function testGetCurrentVersionWithoutDefinedBranchTypeFails()
    {
        $this->vcs->expects($this->any())
                ->method('getCurrentBranch')
                ->will($this->returnValue('develop'));
        $this->detector = new GitFlowBranch($this->vcs);
        
        $this->setExpectedException("\bonndan\ReleaseFlow\Exception", "Cannot detect release or hotfix branch.");
        $this->detector->getCurrentVersion();
    }

    public function testIsInTheFlowWithRelease()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentBranch')
                ->will($this->returnValue('release/2.2.2'));

        $this->assertTrue($this->detector->isInTheFlow());
    }
    
    public function testIsInTheFlowWithHotfix()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentBranch')
                ->will($this->returnValue('hotfix/2.2.2'));

        $this->assertTrue($this->detector->isInTheFlow());
    }
    
    public function testIsNotInTheFlow()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentBranch')
                ->will($this->returnValue('develop'));

        $this->assertFalse($this->detector->isInTheFlow());
    }
}
