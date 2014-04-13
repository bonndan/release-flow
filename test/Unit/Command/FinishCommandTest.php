<?php

namespace bonndan\ReleaseFlow\Tests\Unit\Command;

use bonndan\ReleaseFlow\Command\FinishCommand;
use bonndan\ReleaseFlow\Version;

/**
 * Tests the finish command.
 * 
 * 
 */
class FinishCommandTest extends CommandTest
{
    /**
     * system under test
     * @var FinishCommand
     */
    private $command;
    
    public function setUp()
    {
        parent::setUp();
        $this->command = new FinishCommand();
        $this->command->setReleaseFlowDependencies($this->flow, $this->vcs, $this->composerFile);
        $this->simulateHelperSet($this->command);
    }
    
    public function testThrowsExceptionIfNotInTheFlow()
    {
        $this->flow->expects($this->once())
                ->method('isInTheFlow')
                ->will($this->returnValue(false));
        
        $this->setExpectedException("\bonndan\ReleaseFlow\Exception");
        $this->command->run($this->input, $this->output);
    }
    
   
    
    public function testThrowsExceptionIfComposerVersionNotLess()
    {
        $this->flow->expects($this->once())
                ->method('isInTheFlow')
                ->will($this->returnValue(true));
        
        $this->flow->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.2')));
        $this->composerFile->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.2.1')));
        
        $this->setExpectedException("\bonndan\ReleaseFlow\Exception", "not greater");
        $this->command->run($this->input, $this->output);
    }
    
    public function testAsksForConfirmation()
    {
        $this->flow->expects($this->once())
                ->method('isInTheFlow')
                ->will($this->returnValue(true));
        $this->flow->expects($this->once())
                ->method('getBranchType')
                ->will($this->returnValue(Version\Detector\GitFlowBranch::RELEASE));
        
        $this->flow->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.2')));
        $this->composerFile->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.1')));
        
        $this->dialog->expects($this->once())
                ->method('askConfirmation')
                ->will($this->returnValue(false));
        $this->vcs->expects($this->never())
                ->method('finishRelease')
                ;
        
        $this->command->run($this->input, $this->output);
    }
    
    public function testFinishesRelease()
    {
        $this->flow->expects($this->once())
                ->method('isInTheFlow')
                ->will($this->returnValue(true));
        $this->flow->expects($this->once())
                ->method('getBranchType')
                ->will($this->returnValue(Version\Detector\GitFlowBranch::RELEASE));
        
        $this->flow->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.2')));
        $this->composerFile->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.1')));
        $this->composerFile->expects($this->once())
                ->method('setVersion');
        $this->vcs->expects($this->once())
                ->method('saveWorkingCopy')
                ;
        
        $this->dialog->expects($this->once())
                ->method('askConfirmation')
                ->will($this->returnValue(true));
        $this->vcs->expects($this->once())
                ->method('finishRelease')
                ;
        
        $this->command->run($this->input, $this->output);
    }
    
    public function testFinishesHotfix()
    {
        $this->flow->expects($this->once())
                ->method('isInTheFlow')
                ->will($this->returnValue(true));
        $this->flow->expects($this->once())
                ->method('getBranchType')
                ->will($this->returnValue(Version\Detector\GitFlowBranch::HOTFIX));
        
        $this->flow->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.2')));
        $this->composerFile->expects($this->once())
                ->method('getCurrentVersion')
                ->will($this->returnValue(new Version('0.1.1')));
        $this->composerFile->expects($this->once())
                ->method('setVersion');
        $this->vcs->expects($this->once())
                ->method('saveWorkingCopy')
                ;
        
        $this->dialog->expects($this->once())
                ->method('askConfirmation')
                ->will($this->returnValue(true));
        $this->vcs->expects($this->once())
                ->method('finishHotfix')
                ;
        
        $this->command->run($this->input, $this->output);
    }
    
}