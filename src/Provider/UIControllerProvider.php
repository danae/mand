<?php
namespace Mand\Provider;

use Mand\Model\Url;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class UIControllerProvider implements ControllerProviderInterface
{
  // Redirect URLs if entered directly
  public function redirect(Url $url, Application $app)
  {
    return $app->redirect($url->getUrl());
  }

  // Render the home page
  public function home(Application $app)
  {
    return $app['twig']->render('home.twig',[
      'app' => $app,
      'urls' => $app['urls']->findAll()
    ]);
  }

  // Connect to the app
  public function connect(Application $app): ControllerCollection
  {
    // Create controllers
    $controllers = $app['controllers_factory'];

    // Redirect URLs if entered directly
    $app->get('/{url}',[$this,'redirect'])
      ->assert('url','[a-z0-9]{4,}')
      ->convert('url',function($id) use ($app) {
        return $app['urls']->find($id);
      })
      ->bind('route.redirect');

    // Render the home page
    $app->get('/',[$this,'home'])
      ->bind('route.home');

    // Return the controllers
    return $controllers;
  }
}
