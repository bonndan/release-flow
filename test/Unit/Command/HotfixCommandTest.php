<?php

namespace bonndan\ReleaseFlow\Tests\Unit\Command;

use bonndan\ReleaseFlow\Command\HotfixCommand;
use bonndan\ReleaseFlow\Version;

/**
 * Tests the hotfix command.
 * 
 * 
 */
class HotfixCommandTest extends CommandTest
{
    /**
     * system under test
     * @var StartCommand
     */
    private $command;
    
    public function setUp()
    {
        parent::setUp();
        $this->command = new HotfixCommand();
        $this->command->setReleaseFlowDependencies($this->flow, $this->vcs, $this->composerFile);
        $this->simulateHelperSet($this->command);
    }
    
    public function testThrowsExceptionIfInTheFlow()
    {
        $this->flow->expects($this->once())
                ->method('isInTheFlow')
                ->will($this->returnValue(true));
        
        $this->setExpectedException("\bonndan\ReleaseFlow\Exception");
        $this->command->run($this->input, $this->output);
    }
    
    public function testStartsHotfix()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.2')));
        $this->vcs->expects($this->once())
                ->method('startHotfix')
                ->will($this->returnCallback(array($this, 'startHotfixCallback')));
        
        
        $this->command->run($this->input, $this->output);
    }
    
    public function startHotfixCallback(Version $version)
    {
        $this->assertEquals('0.1.3', $version->getVersion());
    }
}