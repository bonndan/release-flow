Release with the flow.
======================

This project is going to be a successor of release-manager. It will be a zero-conf tool 
to version PHP project releases semantically, tightly coupled with composer and git-flow.

![screenshot](https://github.com/bonndan/release-flow/raw/develop/doc/screen.png "Usage")


Usage:
------

    ```bash
    release-flow start
    release-flow finish
    ```

or to hotfix (patch-bump version based on master branch)

    ```bash
    release-flow hotfix
    release-flow finish
    ```

Installation:
-------------

Download the phar from github, then chmod and move it to your $PATH:

   ```bash
   chmod 755 release-flow.phar
   sudo mv release-flow.phar /usr/local/bin/release-flow
   ```

Optionally you can checkout the project and install it using [phar-composer](https://github.com/clue/phar-composer).


Related projects:
-----------------

* [composer](https://github.com/composer/composer)
* [git-flow](https://github.com/nvie/gitflow)
* [Release Manager](https://github.com/bonndan/release-manager)
* [RMT](https://github.com/liip/RMT)