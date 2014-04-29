<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $output;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^I am in a git repo directory$/
     */
    public function iAmInAGitRepoDirectory()
    {
        $dir = getcwd();
        if (!is_dir($dir . '/.git')) {
            throw new Exception('Cwd is not a git repo.');
        }
    }

    /** @When /^I run "([^"]*)"$/ */
    public function iRun($command)
    {
        $returnVar = null;
        exec($command, $output, $returnVar);
        $this->output = trim(implode("\n", $output));
    }

    /** @Then /^I should get:$/ */
    public function iShouldGet(PyStringNode $string)
    {
        if ((string) $string !== $this->output) {
            throw new Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

    /**
     * @Then /^output should contain$/
     */
    public function outputShouldContain(PyStringNode $string)
    {
        if (strpos($this->output, (string)$string) === false) {
            throw new Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }
}
