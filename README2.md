Gunner Technology WordPress Network
===================================

This document describes how to setup, develop and deploy the Gunner Technology WordPress network.

Installation
------------

Before you get started, you need to have followed the instructions [found here](https://github.com/gunnertech/gunnertechdotcom)

For our purposes, you should follow all the instructions up to "Heroku project setup" which you don't need to do for this.

The exception is that you still need to email cody your heroku account email so he can give you access to the Heroku app. 

### Install HHVM

    $ brew update
    $ brew tap homebrew/dupes
    $ brew tap homebrew/versions
    $ brew tap hhvm/hhvm
    $ brew install hhvm

### Install Composer

    $ homebrew/php/composer
    $ alias composer='hhvm /usr/local/bin/composer'

Add that same line to your ~/.bash_profile by opening that file and pasting that same line at the bottom and saving

### Install S3sync
Download the latest from the githup repository (https://github.com/s3tools/s3cmd) and unzip it (doesn't matter where).

You'll also need to get the AWS Access key and AWS Secret key from Cody.

The installer will prompt you for these. For everything else, just use the defaults by hitting "Enter"

    $ cd ~/Downloads/s3cmd-master #or whever you donwloaded it to
    $ sudo python setup.py install
    $ s3cmd --configure

### Setup the code

    $ mkdir -p ~/workspace/php/wordpress
    $ cd ~/workspace/php/wordpress
    $ git clone https://github.com/gunnertech/wordpress.git
    $ cd ~/workspace/php/wordpress/wordpress 
    ### follow prompt to install correct ruby
    $ cd .. && cd wordpress
    $ gem install bundler --no-ri --no-rdoc
    $ bundle install
    $ composer update --ignore-platform-reqs
    $ cap production all:assets:deploy 
    $ cap production wordpress:assets:deploy 
    $ cap production wp_admin:assets:deploy

### Connect your code to Heroku

    $ cd ~/workspace/php/wordpress/wordpress
    $ heroku git:remote -a gunnertechnetwork -r production


### Test it out

    $ touch README.md
    $ git add .; git commit -am "Testing my setup"; git push origin production; git push production production:master

Development
-----------

TODO: Setup vagrant and all that crap

Deployment
----------

The deploy process initially is identical to any deployment to Heroku:

    $ git add .; git commit -am "<commit message>"; git push origin production; git push production production:master

However, with the network, we potentially have additional steps to follow.

Static assets are not included in the slug deployed to Heroku as it would exceed size limits, so we have to sync our files with S3.

Please note, if you are sure that your updates/additions/changes have not modified or added any static assets, you can skip this step.

### Plugin Addition/Update

After a plugin is updated, run the following:

    $ cap production plugin:assets:deploy

When prompted, type the name of the plugin as it appears as the directory name

### Theme Addition/Update

After a theme is updated, run the following:

    $ cap production theme:assets:deploy

When prompted, type the name of the theme as it appears as the directory theme

### Wordpress Update

After Wordpress is updated, run the following:

    $ cap production wordpress:assets:deploy
    $ cap production wp_admin:assets:deploy

### Global update

If you update a bunch of themes and plugins at once, you can deploy all their assets at once with the following:

    $ cap production all:assets:deploy

Right now, after you run this, open deploy.rb and change line 85 to match the date you ran this. This is not 100% necessary, but it will save you a lot of time the next time you run the global updater.

TODO: Automate the updating of the deploy timestamp.

Please note, the global update does not update the WordPress and WP Admin assets. You'll have to run those command separetly.

### Adding a new plugin

As long as the plugin is hosted with the wordpress codex (as most are), you just add the plugin in composer.json and run the following

    $ composer update --ignore-platform-reqs

However, if the plugin is in-house or paid for, you must add it manually to public/wp-content/plugins.

### Updating existing plugins

Again, upgrading most of the plugins is simply a matter of running the compser update and then deploying the changes as described above.

However, if it's a plugin that was added manually, it most be done manually again by basically deleting the old plugin and putting the new version in it's place inside public/wp-content/plugins

### Adding a new theme

All the themes we have are added manually - just like a plugin - except they go in public/wp-content/plugins

And just like manually added plugins, themes must be updated manually.

### Adding a new site

For this, you'll need to login to the Newtwork Admin (get the username and password from Cody) so go [here](http://gunnertechnetwork.com/gunnerlogin/) first to login and then once you're logged in, go [here](http://gunnertechnetwork.com/wp-admin/network/).

In the left-hand navigation, click "Add New" under the "Sites" nav. 

This will take you here - http://gunnertechnetwork.com/wp-admin/network/site-new.php

Fill in the info and click "Add Site"

You should immediately be able to browse to the url, which will look something like this: http://testing.gunnertechnetwork.com/, and view the new site.

#### Configuring Uploads

Before you can start uploading images to the new site, you need to configure the S3 Plugin.

To do so, go to the new site's dashboard, which will look something like this: http://testing.gunnertechnetwork.com/wp-admin/

Look under settings for a link that says "Amazon S3" - click it and go to a page that will look like this: http://testing.gunnertechnetwork.com/wp-admin/options-general.php?page=tantan-s3-cloudfront%2Fwordpress-s3%2Fclass-plugin.php

Change the AWS Access Key and the Secret Key to match the ones you got for "S3sync" and then click "Authenticate Account"

Change the settings to match the following:
* Use this bucket: gunnertechnetwork
* Host name settings: <unchecked>
* File Uploads: <checked>
* Expires Header: <checked>
* File Permissions: <checked>
* Cloud Front: dhwlijwe9jil7.cloudfront.net

That's all you have to do to get the site up and running. 

However, when you're ready to go live, your client probably will want to use their own URL.

To do that, follow the following steps.

#### Add the domain to the network's mapping.

Go back to the [network admin dashboard]((http://gunnertechnetwork.com/wp-admin/network/)).

You're going to need the site's id.

To do that, click the "Sites" nav item, which will take you here: http://gunnertechnetwork.com/wp-admin/network/sites.php

Find the new site and click "edit" next to it, which will take you to a url like this: http://gunnertechnetwork.com/wp-admin/network/site-info.php?id=201

That id is what we want, so copy it or make a note of it.

Then, under the "Settings" nav, click "Domains" which will take you here: http://gunnertechnetwork.com/wp-admin/network/settings.php?page=dm_domains_admin

Add the new site by typing in the site id from above and the desired URL.

PLEASE NOTE: The desired URL cannot be a top-level domain. 

So testing.com will not work, however, www.testing.com will work.

#### Add the domain to Heroku

We need to let the Heroku app know about the new url.

To do that, run the following from the project's root directory

    $ heroku domains:add www.testing.com -r production

#### Configure the new site's DNS

These instructions will vary depending on the domain provider, but what you need to do is the following:

* Add a cname record to gunnertechnetwork.herokuapp.com

Usually the cname will be "www" just make sure it's whatever you used in the above steps

* Change the naked A Record to 174.129.25.170

