<?php
namespace bonndan\ReleaseFlow\Tests\Unit\Command;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Base class for command tests.
 * 
 * 
 */
abstract class CommandTest extends PHPUnit_Framework_TestCase
{
    protected $input;
    
    protected $output;
    
    protected $dialog;
    
    protected $flow;
    
    protected $vcs;
    
    protected $composerFile;
    
    public function setUp()
    {
        $this->input = $this->getMock("\Symfony\Component\Console\Input\InputInterface");
        $this->output = $this->getMock("\Symfony\Component\Console\Output\OutputInterface");
        $this->flow = $this->getMockBuilder("\bonndan\ReleaseFlow\Version\Detector\GitFlowBranch")
                ->disableOriginalConstructor()
                ->getMock();
        $this->composerFile = $this->getMockBuilder("\bonndan\ReleaseFlow\Version\ComposerFile")
                ->disableOriginalConstructor()
                ->getMock();
        $this->vcs = $this->getMock("\bonndan\ReleaseFlow\VCS\VCSInterface");
    }
    
    public function simulateHelperSet(Command $command)
    {
        $this->dialog = $this->getMock("Symfony\Component\Console\Helper\DialogHelper");
        $helpers = array(
            'dialog' => $this->dialog,
        );
        $helperSet = new HelperSet($helpers);
        $command->setHelperSet($helperSet);
    }
}