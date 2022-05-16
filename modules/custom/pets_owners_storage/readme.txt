useful link:

https://codimth.com/blog/web/drupal/how-use-database-api-creating-custom-form-crud-operations-drupal-8

3. //get data
         $rows[] = [
           'id' => $data->poid,
           ........

           https://qna.habr.com/q/187405


Ajax API task:2

1. array_map()
https://www.php.net/manual/ru/function.array-map.php

2. use table bild rows 52-72
module example -> dbtng_example -> DbtngExampleController.php

3. \Drupal::service('renderer')->render($ajax_link) -> Renderer::render
https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Renderer.php/class/Renderer/8.2.x


useful link:
https://internetdevels.ua/blog/creating-popups-in-Drupal-8
https://niklan.net/blog/125#primer-no3-eschyo-raz-render-array
https://www.drupal.org/docs/drupal-apis/ajax-api/ajax-dialog-boxes


Views customization

useful link:
https://zanzarra.com/blog/custom-views-filter-plugin-drupal-8-bounding-box-geofield
https://gorannikolovski.com/blog/custom-views-filter-plugin-drupal
https://www.youtube.com/watch?v=ZQELm9okEdc

REST API

https://www.drupal.org/docs/drupal-apis/restful-web-services-api/custom-rest-resources
https://www.youtube.com/watch?v=xbBlbEcJmSo
https://niklan.net/blog/165
https://www.valuebound.com/resources/blog/create-rest-resource-for-get-method-drupal-8


drupal2022/api/pets_owners/v1/get-pets-owners/?id=2&_format=json

ModifiedResourceResponse - не кешується
ResourceResponse - кешується, для тесту не використовувати


