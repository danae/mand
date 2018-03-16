<?php
namespace Propel\Provider;

use Propel\Model\Url\Url;
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
      $longUrl = $request->query->get('url');
    else
      throw new BadRequestHttpException("You must provide a 'url' parameter");
    
    // Check if the URL is a valid URL
    if (filter_var($longUrl,FILTER_VALIDATE_URL,FILTER_FLAG_SCHEME_REQUIRED & FILTER_FLAG_HOST_REQUIRED) === false)
      throw new BadRequestHttpException("The specified URL is no valid URL");
    
    // Check if the URL starts with the base URL
    if (strpos($longUrl,$app['base_url']) === 0)
      throw new BadRequestHttpException("The specified URL is already a shortened URL");
      
    // Check if an URL for this long URL already exists
    if (($url = $app['urls']->findByLongUrl($longUrl)) == null)
    {
      // No, so create a new URL
      $url = Url::create(
        $app['base_url'] . $app['generator']->generate(),
        $longUrl
      );
    
      // Create the URL in the repository
      $app['urls']->create($url);
    }
    
    // Return the URL
    $json = $app['json_serializer']->serialize($url,'json');
    return JsonResponse::fromJsonString($json);
  }
  
  // Get an URL
  public function lookup(Request $request, Application $app)
  {
    // Check if the request is complete
    if ($request->query->has('url'))
      $shortUrl = $request->query->get('url');
    else
      throw new BadRequestHttpException("You must provide a 'url' parameter");
    
    // Fetch the URL
    $url = $app['urls']->findByShortUrl($shortUrl);
        
    // Return the URL
    $json = $app['json_serializer']->serialize($url,'json');
    return JsonResponse::fromJsonString($json);
  }
  
  // Connect to the app
  public function connect(Application $app): ControllerCollection
  {
    // Create controllers
    $controllers = $app['controllers_factory'];
    
    // Shorten a long URL
    $controllers->get('/shorten',[$this,'shorten'])
      ->before('authorization:authorize')
      ->bind('route.api.shorten');

    // Look up a short URL
    $controllers->get('/lookup',[$this,'lookup'])
      ->before('authorization:authorize')
      ->bind('route.api.lookup');
    
    // Return the controllers
    return $controllers;
  }
}
