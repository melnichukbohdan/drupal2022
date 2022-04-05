Entity API

task 2-4
https://www.hashbangcode.com/article/drupal-9-some-strategies-developing-update-hooks

drush command for run hooks :
drush php:eval "module_load_include('install', 'smile_test');smile_test_update_9006();" or
drush updatedb for batch functionality
