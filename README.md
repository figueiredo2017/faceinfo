Face Detection and Information
========

This app is my first usage of Symfony. It uses Face++ API to detect faces to return how many people in the picture, estimate age, ethnicity and gender.

Technology Used
---------------

* Bootstrap 4
* Symphony
* Vich/Uploader-bundle
* Unirest

Live Version
---------------
A live version can be viewed [here](http://figueiredoluiz.com/projects/faceinfo/web/) .

Edited Files
---------------
* src/AppBundle/Controller/DefaultController.php
* src/AppBundle/Entity/Faces.php
* src/AppBundle/Form/FacesType.php
* app/Resources/base.html.twig
* app/Resources/views/default/about.html.twig
* app/Resources/views/default/index.html.twig
* app/Resources/views/default/result.html.twig
* web/css/site.css
* web/.htaccess