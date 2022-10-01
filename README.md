# select2entity-bundle-demo
Demo for select2entity-bundle (https://github.com/tetranz/select2entity-bundle).  It shows various ways to select a country or countries from a jquery-select2 input element.

## Create This Demo Locally

### Requirements

* composer
* PHP 8
* yarn
* Symfony Server (for local testing)

### Run the demo locally

```bash
git clone git@github.com:survos/select2entity-bundle-demo.git sel2-demo && cd sel2-demo 
composer install
bin/console doctrine:schema:update --force && bin/console doctrine:fixtures:load -n
yarn install
yarn run encore dev
symfony serve
```

See https://askubuntu.com/questions/992448/how-to-execute-a-bash-script-from-github to run all this...

### Setup

These are the steps to recreate this demo locally. 

```bash
DIR=select-demo && mkdir $DIR && cd $DIR && symfony new --webapp . 
composer req survos/maker-bundle --dev
echo "DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db" > .env.local

# create the entity from symfony/maker-bundle
cat <<'EOF' | sed "s/,/\n/g"  |  bin/console make:entity Country
name,string,55,no,,
alpha2,string, 2,no,
EOF

# commands that come from a maker namespace don't have access to STDIN (for some reason), so create the class first.
symfony console survos:make:command app:import-countries "Import the countries from symfony/intl to the database"

echo "x" | symfony console survos:class:update  App\\Command\\AppImportCountriesCommand -m __invoke \n
  --use Doctrine\EntityManagerInterface
  --inject EntityManagerInterface$em


cat <<'EOF' | symfony console make:method  App\\Command\\AppImportCountriesCommand -m __invoke --inject Doctrine\EntityManagerInterface$em
// the invoke body goes here, NOT the entire signature
        $countries = \Symfony\Intl\Countries::getNames();
        foreach ($countries as $alpha2=>$name) {
            $country = new Country();
            $country
                ->setName($name)
                ->setAlpha2($alpha2);
            $this-em->persist($country);
        }
        $this-em->flush();
EOF
git diff src/Command


```
    
We need an entity class, we're going to use Country, and populate the table using Symfony's intl component.  We'll need a database, if you're running locally and have sqlite installed, use that.  Or any database that Doctrine supports.  Later we'll move this to postgres for heroku.  By default, Symfony assumes you're using a mysql database, so change it in .env.local


    # make the entity / repo


git clean src/Service/ -f
bin/console make:service X
// generates XService in src/Service

// in SurvosMakerBundle, using a Maker (for prompting)

tmpfile=$(mktemp /tmp/snippet)

snippet_file=$(mktemp)

cat <<'EOF' > $snippet_file
// src/Service/XService.php
public function test(): void {}
EOF

cat <<'EOF' > 
// src/Service/XService.php
public function test(): void {}
EOF
bin/console x:make:method --body $


cat > xx <<END_OF_HEREDOC_MARKER
defscrollback ${MAX_LINES}
END_OF_HEREDOC_MARKER


cat $snippet_file

: ...
rm "$tmpfile"

cat <<'EOF' > 
// src/Service/XService.php
public function test(): void {}
EOF
cat <<'EOF' | symfony console make:method  AppService -m calcScore
// src/Service/XService.php
public function test(): void {}
EOF



echo "x" | bin/console make:method




    bin/console make:entity Country
       # name, string, 55, no (not nullable)
       # alpha2, string, 2, no (not nullable)
       
    bin/console doctrine:schema:update --force

    composer require make orm-fixtures --dev
    composer require symfony/intl 

    bin/console make:fixtures CountryFixtures
    
Loading the database is trivial, 

```
use Symfony\Component\Intl\Countries;
use App\Entity\Country;

class CountryFixtures extends Fixture
{
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

Load the countries

```bash
symfony console doctrine:fixtures:load -n 
```

It relies on bootstrap and jquery, loaded via Webpack Encore.  Although this is more setup than simply loading those libraries from a CDN, it is also a best practice and more representative of a real-world application.

```bash
composer req symfony/webpack-encore-bundle && yarn install
```
`    
Get bootstrap and jquery

```bash
yarn add bootstrap jquery select2@4.0.6 @popperjs/core
```
    
and add them to app.js and app.css to make them global.  The select2 configuration is all done in PHP (via data-* attributes) so we will simpmly initialize the appropriate elements here.

```javascript
// app.js
require('jquery');
require('bootstrap');
require('../../vendor/tetranz/select2entity-bundle/Resources/public/js/select2entity.js');

// initialize the select2 elements.
$('.js-select')
````

```css
/* assets/app.css */
@import "~select2/dist/css/select2.min.css";
@import "~bootstrap/dist/css/bootstrap.min.css";
```

Compile the assets.  First allow a global jQuery object

```js

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

```

```bash
yarn run encore dev
```

## Bootstrap Bundle

This helper bundle gives us a basic landing page, a base that loads the assets, and some menus.

    composer require survos/bootstrap-bundle
    
Replace base.html.twig so that it extends the base from the landing bundle.  This will load the css and javascript from the compiled webpack.

echo '{% extends "@SurvosBootstrap/sneat/base.html.twig" %}' >templates/base.html.twig


## Finally, start using select2EntitiesBundle

Now we've got a basic website with an entity, and we want to create some pages and forms.

    composer req tetranz/select2entity-bundle
    
Update twig.yaml to include rendering select2 

```yaml
    form_themes:
        - 'bootstrap_4_horizontal_layout.html.twig'
        - '@TetranzSelect2Entity/Form/fields.html.twig'    
```    

### Create a Form and Add the country field    

```bash

    bin/console make:form SingleSelectForm

```
Open up your form and configure the field, e.g.

```php
            ->add('single_country', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'app_country_autocomplete',
                'class' => Country::class,
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 1,
                'cache' => 0,
                'page_limit' => 10,
                'required' => false,
                'allow_clear' => true,
                'language' => 'en',
                'placeholder' => 'Select A Single Country',
                'attr' => [
                    'class' => 'js-select2'
                ]
            ])
```


The 'class' in 'attr' is very important, since we use that in app.js to initialize.
```

### Create and Configure the Controller

```bash
    bin/console make:controller AppController
```


The autocomplete ajax call is a simple query, using the repository created in make:entity.

```php

// add to AppController.php

    /**
     * @Route("/country_autocomplete.json", name="app_country_autocomplete")
     */
    public function CountryAutocomplete(Request $request, CountryRepository $repository)
    {
        $q = $request->get('q');
        $matches = $repository->createQueryBuilder('c')
            ->where("c.name LIKE :searchString")
            ->setParameter('searchString', $q . '%')
            ->getQuery()
            ->getResult();

        $data = array_map(function(Country $country) use ($request) {
            return ['id' => $country->getId(), 'text' => $country->getName()];
        }, $matches);
        $data = array_values($data);

        $data = ['results' => $data];
        return new JsonResponse($data);
    }
```

Of course, you need a route to land on, then you'll instanciate the form and send it to be rendered in a twig template.  

```php
// add to AppController.php
    /**
     * @Route("/", name="home")
     */
    public function showForm(Request $request)
    {
        $defaults = [];
        $form = $this->createForm(\App\Form\SingleSelectFormType, $defaults);

        return $this->render('app/showForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
```

## Compile the assets and run

```bash
yarn  dev
symfony serve
```

Open the web page, and you should now have a select2 form.



## Heroku

Initialize heroku and add a database

    heroku create <name>
    heroku addons:create heroku-postgresql:hobby-dev

Add node to buildpack

    heroku buildpacks:add heroku/nodejs
    git push heroku main  
    
Add Sentry to make your life easier!


      




