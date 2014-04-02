<?php

namespace bonndan\ReleaseFlow\Tests\Unit\Command;

use bonndan\ReleaseFlow\Command\StartCommand;
use bonndan\ReleaseFlow\Version;

/**
 * Tests the start command.
 * 
 * 
 */
class StartCommandTest extends CommandTest
{
    /**
     * system under test
     * @var StartCommand
     */
    private $command;
    
    public function setUp()
    {
        parent::setUp();
        $this->command = new StartCommand($this->flow, $this->vcs);
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
    
    public function testStartsRelease()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.2')));
        $this->dialog->expects($this->once())
                ->method('select')
                ->will($this->returnValue(Version::TYPE_MINOR));
        $this->vcs->expects($this->once())
                ->method('startRelease')
                ->will($this->returnCallback(array($this, 'startReleaseCallback')));
        
        
        $this->command->run($this->input, $this->output);
    }
    
    public function startReleaseCallback(Version $version)
    {
        $this->assertEquals('0.2.0', $version->getVersion());
    }
}