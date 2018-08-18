<?php
require "vendor/autoload.php";

use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Mand\Generator\Generator;
use Mand\Implementation\MongoDB\UrlRepository;
use Mand\Implementation\MongoDB\UserRepository;
use Mand\Model\Authorization\BasicAuthorization;
use Mand\Provider\APIControllerProvider;
use Mand\Provider\UIControllerProvider;
use MongoDB\Client;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

// Register error handlers
ErrorHandler::register();

// Create the application with the settings
$app = new Application(require('settings.php'));
$app['debug'] = true;
$app['base_url'] = 'https://prpl.lu/';

// Register services
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(),[
  'twig.path' => __DIR__ . '/templates',
  'twig.options' => ['autoescape' => false]
]);

// Parse the request body if JSON
$app->before(function(Request $request)
{
  if (strpos($request->headers->get('Content-Type'),'application/json') === 0)
  {
    $data = json_decode($request->getContent(),true);
    $request->request->replace(is_array($data) ? $data : []);
  }
});

// Pretty print the JSON response
$app->after(function(Request $request, Response $response)
{
  if ($response instanceof JsonResponse)
    $response->setEncodingOptions(JSON_PRETTY_PRINT);
  return $response;
});

// Add support for CORS requests
$app->register(new CorsServiceProvider);
$app['cors-enabled']($app);

// Create the services
$app['database'] = function($app) {
  return (new Client($app['mongodb.connection']))->{$app['mongodb.database']};
};
$app['serializer'] = function() {
  return new Serializer([new DateTimeNormalizer('Y-m-d H:i:s'),new GetSetMethodNormalizer],[]);
};
$app['json-serializer'] = function() {
  return new Serializer([new DateTimeNormalizer(DateTime::ISO8601),new CustomNormalizer],[new JsonEncoder]);
};
$app['users'] = function(Application $app) {
  return new UserRepository($app['database']->users,$app['serializer']);
};
$app['urls'] = function(Application $app) {
  return new UrlRepository($app['database']->urls,$app['serializer']);
};
$app['generator'] = function(Application $app) {
  return new Generator($app['urls']);
};
$app['authorization'] = function(Application $app) {
  return new BasicAuthorization($app['users'],'prpl.lu');
};

// Create the controllers
$app['api-controller'] = function() {
  return new APIControllerProvider();
};
$app['ui-controller'] = function() {
  return new UIControllerProvider();
};

// Mount the controllers
$app->mount('/api',$app['api-controller']);
$app->mount('/',$app['ui-controller']);

// Run the application
$app->run();
