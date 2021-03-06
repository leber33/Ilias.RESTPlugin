<?php
/*
 * Mobile Search Routes
 */

$app->group('/m/v1', function () use ($app) {


    $app->get('/search/',  function () use ($app) {
        $request = new ilRestRequest($app);
        $response = new ilRestResponse($app);

        try {
            $query = utf8_encode($request->getParam('q'));
        } catch (Exception $e) {
            $query = '';
        }

        $model = new ilMobileSearchModel();
        $searchResults = $model->performSearch($query);

        $response->addData('search_results', $searchResults);
        $response->setMessage('You have been searching for: "'.$query.'"');
        $response->send();
    });

    $app->post('/search/',  function () use ($app) {
        $request = new ilRestRequest($app);
        $response = new ilRestResponse($app);

        try {
            $query = utf8_encode($request->getParam('q'));
        } catch (Exception $e) {
            $query = '';
        }

        $model = new ilMobileSearchModel();
        $searchResults = $model->performSearch($query);

        $response->addData('search_results', $searchResults);
        $response->setMessage('You have been searching for: "'.$query.'"');
        $response->send();
    });


});
