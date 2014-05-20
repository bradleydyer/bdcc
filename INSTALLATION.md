# How to install BDCC
## Installing on your Ubuntu Dev Container

 - make sure you have phpunit

        phpunit --version

    This should give you response similar to:
    
    `PHPUnit 4.0.18 by Sebastian Bergmann.`
        
    If you don't have phpunit follow these [instrucions](http://phpunit.de/manual/3.7/en/installation.html)

 - go to /home/sites and clone the source

        cd /home/sites && git clone git@github.com:{YOUR_GITHUB_USERNAME}/BDCC.git

 - add bradley dyer as remote

        git remote add bd git@github.com:bradleydyer/BDCC.git

 - add post-merge hook to your git config

        vi .git/hooks/post-merge

   Add following line:

        git branch --merged master | grep -v 'master$' | xargs git branch -d
        
   Save you changes: ESC, :wq
   
   Change permissions on the file you have just created:
   
        chmod 0755 .git/hooks/post-merge
