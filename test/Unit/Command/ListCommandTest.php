<?php

namespace bonndan\ReleaseFlow\Tests\Unit\Command;

use bonndan\ReleaseFlow\Command\ListCommand;
use bonndan\ReleaseFlow\Version;

/**
 * Tests the list command.
 * 
 * 
 */
class ListCommandTest extends CommandTest
{
    /**
     * system under test
     * @var FinishCommand
     */
    private $command;
    
    public function setUp()
    {
        parent::setUp();
        $this->command = new ListCommand();
        $this->command->setReleaseFlowDependencies($this->flow, $this->vcs, $this->composerFile);
        $this->simulateHelperSet($this->command);
    }
    
    public function testDisplaysErrorMessage()
    {
        $this->vcs->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.2')));
        $this->composerFile->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.1')));
        
        $this->output->expects($this->exactly(2))
                ->method('writeln')
                ;
        
        $this->command->checkVersions($this->output);
    }
    
}