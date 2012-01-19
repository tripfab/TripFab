# Welcome to the TripFab application repository

## About this repository

In this repo you will find three projects in one so be careful what file are you editing

+ _/application2_ and _/html_				Contain the application for www.tripfab.com
+ _/appvendors_ and _/partners_				Contain the application for partners.tripfab.com
+ _/appdev and _/html/d3E3v8E3l5O6p7E7r3/	Contain the application for developer.tripfab.com

The three of them share the _/library_ folder where you can find usefull classes. _/library/WS/ contains the service layer classes pf the application, 

### How to contribute

First you need to be a freelance or inhouse developer hired by TripFab Ink and authorized by the application manager

If you are one of them just clone the project and start commiting, pulling and pushing updates

### How to install

Install it's supper easy

1. Off course you need to have git ready on your machine
1. Clone the project with git to your computer
2. Create a database and a database user with the configuration at /appdev/application.ini
3. Create 3 virtual hosts one for each application described above for example:
	+ http://tripfab.www        pointing to /html
	+ http://tripfab.partners   pointing to /partners
	+ http://tripfab.developer	pointing to /html/d3E3v8E3l5O6p7E7r3/
4. When visiting http://tripfab.developer You will be asked for an username and password use: -u worldspot    -p admin2011Ws
5. Start working on the issues and features assigned to you
6. Commit every issue or feature you finish starting with : Fixed #<issue_id> Where <issue_id> is the number of the issue
6. When you are done for the day PULL a copy of the application and merge with your copy Fix all the conflict that may occur 
7. PUSH the changes to the repo and go to sleep