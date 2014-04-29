Feature: git-flow command
  In order to use release-flow
  I require the git-flow cli command

  Scenario: Run git-flow
    Given I am in a git repo directory
    When I run "git flow"
    Then output should contain
      """
      usage: git flow <subcommand>
      """