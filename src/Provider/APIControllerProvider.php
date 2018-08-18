<?php
namespace Mand\Provider;

use Mand\Model\Url;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class APIControllerProvider implements ControllerProviderInterface
{
  // Shorten a long URL
  public function shorten(Request $request, Application $app)
  {
    // Check if the request is complete
    if ($request->query->has('url'))
      $url = $request->query->get('url');
    else
      throw new BadRequestHttpException("You must provide a 'url' parameter");

    // Check if the URL is a valid URL
    if (filter_var($url,FILTER_VALIDATE_URL,FILTER_FLAG_SCHEME_REQUIRED & FILTER_FLAG_HOST_REQUIRED) === false)
      throw new BadRequestHttpException("The specified URL is no valid URL");

    // Check if the URL starts with the base URL
    if (strpos($url,$app['base_url']) === 0)
      throw new BadRequestHttpException("The specified URL is already a shortened URL");

    // Check if an URL for this long URL already exists
    if (($urlInstance = $app['urls']->findByUrl($url)) === null)
    {
      // No, so create a new URL
      $urlInstance = Url::create($app['generator']->generate(),$url);

      // Insert the URL into the repository
      $app['urls']->insert($urlInstance);
    }

    // Return the URL
    $json = $app['json-serializer']->serialize($urlInstance,'json');
    return JsonResponse::fromJsonString($json);
  }

  // Get an URL
  public function lookup(Request $request, Application $app)
  {
    // Check if the request is complete
    if ($request->query->has('id'))
      $id = $request->query->get('id');
    else
      throw new BadRequestHttpException("You must provide a 'id' parameter");

    // Fetch the URL
    $urlInstance = $app['urls']->find($id);

    // Return the URL
    $json = $app['json-serializer']->serialize($urlInstance,'json');
    return JsonResponse::fromJsonString($json);
  }

  // Connect to the app
  public function connect(Application $app): ControllerCollection
  {
    // Create controllers
    $controllers = $app['controllers_factory'];

    // Shorten a long URL
    $controllers->get('/shorten',[$this,'shorten'])
      //->before('authorization:authorize')
      ->bind('route.api.shorten');

    // Look up a short URL
    $controllers->get('/lookup',[$this,'lookup'])
      //->before('authorization:authorize')
      ->bind('route.api.lookup');

    // Return the controllers
    return $controllers;
  }
}
