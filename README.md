# select2entity-bundle-demo
Demo for select2entity-bundle (https://github.com/tetranz/select2entity-bundle).  It shows various ways to select a country or countries from a jquery-select2 input element.

See this in action on heroku, at 

## Create This Demo Locally

### Requirements

* composer
* PHP 7.1+
* yarn

### Setup

These are the steps to recreate this demo locally.  We'll use orm-fixtures to load the database

    composer create-project symfony/website-skeleton select2-demo
    cd select2-demo
    
We need an entity class, we're going to use Country, and populate the table using Symfony's intl component.  We'll need a database, if you're running locally and have sqlite installed, use that.  Or any database that Doctrine supports.  Later we'll move this to postgres for heroku.  By default, Symfony assumes you're using a mysql database, so change it in .env.local

echo "DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db" > .env.local

    # make the entity / repo
    bin/console make:entity Country
       # name, string, 55, no (not nullable)
       # alpha2, string, 2, no (not nullable)
       
    bin/console doctrine:schema:update --force

    composer require make orm-fixtures --dev
    composer require symfony/intl 

    bin/console make:fixtures CountryFixtures
    
Loading the database is trivial, 

```
    // CountryFixtures.php
    public function load(ObjectManager $manager)
    {
        $countries = Countries::getNames();
        foreach ($countries as $alpha2=>$name) {
            $country = new Country();
            $country
                ->setName($name)
                ->setAlpha2($alpha2);
            $manager->persist($country);
        }
        $manager->flush();
    }
```


This helper bundle gives us a basic landing page with some menus.

    composer require survos/landing-bundle

It relies on bootstrap and jquery, loaded via Webpack Encore.  Although this is more setup than simply loading those libraries from a CDN, it is also a best practice and more representative of a real-world application.

    composer symfony/webpack-encore-bundle && yarn install
    
Get bootstrap and jquery

    yarn add bootstrap jquery popper.js
    
and add them to app.js to make them global

```javascript
// app.js
require('jquery');
require('bootstrap');
```

## Finally, select2Bundle

Now we've got a basic website with an entity, and we want to create some pages and forms.

    bin/console make:controller AppController
    bin/console make:form SingleSelectForm


## Heroku

Initialize heroku and add a database

    heroku init
    heroku addons:create heroku-postgresql:hobby-dev

Add node to buildpack

    heroku buildpacks:add heroku/nodejs
    git push heroku master  
    
Add Sentry to make your life easier!


      




