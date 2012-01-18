1. Login to the server with your username and password

	`$ ssh root@tripfab.com`
    
    Password
    
    `devsrv01pstE31P4W`

2. change directory to /var/devlopment/[username] where username is the name assigned by your manager

	`$ cd /var/devlopemnt/[username]`

4. Grab a copy of the project (Just the first time)

	`$ git clone git@github.com:cristian0789/TripFab.git`

when asked for the password copy and paste this

	`devsrv01pstE31P4W`

keep that password close to you as github will ask you for it everytime you run a "pull" or "push" request

Change directories to the Tripfab Folder

	`cd Tripfab/`

5. Run the following commands:
	
    `mkdir html/cache`
    `mkdir html/cache/remote`
    `mkdir html/d3E3v8E3l5O6p7E7r3/cache`
    `mkdir html/d3E3v8E3l5O6p7E7r3/cache/remote`
    `mkdir partners/cache`
    `mkdir partners/cache/remote`
    `mkdir library/Zend/`
    `cp -r /var/www/library/Zend/* library/Zend/`

6. Now you should be able to access all the three applications through

	[username].www.tripfab.com		  
    	// This application is managed by application2 files and the root at html/
    [username].developer.tripfab.com  
    	// This application is managed by appdev files and the root at html/d3E3v8E3l5O6p7E7r3/
    [username].partners.tripfab.com	  
    	// This application is managed by appvendors files and the root at partners/
    
7. My recomendation from here, is to set 4 diferent projects in netbeans or your selected IDE 
		- one for application2
        - one for appdev
        - one for appvendors
        - one for library/WS
        	// This folder contains custom libraries created by programmers
            // needed for the application

8. Every time you are going to start working on an issue or a new feature you need to make a pull request to github to get the latest code available

	`$ git pull

9. Fix any issue you have if any

10. Set up your credentials so we know who is gonna make the changes

	`$ git config user.name "Your Name"
     $ git config user.email "your@email.com"`

11. Start making the changes needed, every time you solve an issue you need to commit the changes run (replace issue_id with the numbre of the issue you are fixing)

	`$ git status"
     $ git add .
     $ git commit -m "The title of the issue you solved. Fixed #[issue_id]"`

12. Once you are done with your work of the day you need to push you changes to GitHub. But before that it's a good practice to pull a copy from GitHun again and work a little bit more in mergin the changes if needed

	`$ git pull`

13. Now you are ready to push

	`$ git push origin master`
    
That would be it please any questions or comments email me at  cristian@tripfab.com