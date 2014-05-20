#How to Contribute

In a nutshell, follow the workflow, and write tested, quality code.  
Getting 'it' out the door at any cost is not the idea.  
You can still ship fast and often doing things properly.

##Commits

* Commits must be small and make sense when viewed in isolation. Your commit message
 must also make sense. The title should be short and be prefixed with the 
 Bundle or section it affects, the body holds the full explanation of the change
 Which again will be fairly short, as your commit is small, sometimes, you won't even need
 a body of the commit message.

###Examples of bad commit messages are

 * Fixes Bugs
 * Changes view because John wanted it this way
 * Minifies file

###Examples of good commit messages are

 * [Layoutbundle] Adds Bootsrap CSS files
 * [config, security] Disabled security for login area
 * [ReportBundle] Calls Controllers error response now using JS
     * The response format of the calls controller when erroring, is now JavaScript
      This is executed by the browser and updates the view rather than having
      the view poll for a response, this is much more efficient


###Auto Closing an Issue

Your commit message should close *one* issue. This is done by using certain words
 followed by a hash and the issue ID like this. Closes #123  
If that was your commit body, you will auto close issue 123 when your PR is merged
and the commit and issue will be linked together.  
This helps when looking back over issues and the revision history.

If there is no issue, you may be making changes based on a basecamp task.  
If that is the case, then please paste the link to the todo item in your comment.

##Pull Requests

* All changes must be done in a topic branch on your fork of the project in 
 the form of a Pull Request (PR)
* All PRs should be small, and include only one change, this makes them easier to
 review, easier for you to write, and easier to document & test

1) Fork the repo.

2) Only pull requests with passing tests are accepted, ensure your base is passing

    phpunit -c app/

3) Add a test for your change. Only refactoring and documentation changes
require no new tests. If you are adding functionality or fixing a bug, we need
a test!

4) Make the test pass.

5) Push to your fork and submit a pull request.

##Getting your PR accepted

Some things that will increase the chance that your pull request is accepted.

* Give your PR a meaningful title for the change, and ensure the description
 is complete and covers what you are doing, and potentially why if relevant.

* Use PSR Coding Standards and Style Guides

    http://symfony.com/doc/current/contributing/code/standards.html  
    https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md  
    https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md  
    https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


* Include tests that fail without your code, and pass with it
* Update the documentation, examples elsewhere, guides,
  whatever is affected by your contribution

Failure to comply with these requirements, will result in your PR not being merged.  
However the PR process is iterative, so you don't need to get it right first time,
we will review, comment and iterate on the solution until it's ready to be merged.
The important thing is not to find the quickest solution, but to find the best solution; Reusable, solid, testable, simple.
