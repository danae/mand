<?php
require "vendor/autoload.php";

use Database\Database;
use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Propel\Generator\Generator;
use Propel\Model\Url\UrlRepository;
use Propel\Model\User\Authorization\BasicAuthorization;
use Propel\Model\User\UserRepository;
use Propel\Provider\APIControllerProvider;
use Propel\Provider\UIControllerProvider;
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
$app->after($app['cors']);

// Create the services
$app['database'] = function($app) {
  return new Database("mysql:host=" . $app['db.server'] . ";dbname=" . $app['db.database'],$app['db.user'],$app['db.password']);
};
$app['serializer'] = function() { 
  return new Serializer([new DateTimeNormalizer('Y-m-d H:i:s'),new GetSetMethodNormalizer],[]);
};
$app['json_serializer'] = function() {
  return new Serializer([new DateTimeNormalizer(DateTime::ISO8601),new CustomNormalizer],[new JsonEncoder]);
};
$app['users'] = function(Application $app) {
  return new UserRepository($app['database'],'users',$app['serializer']);
};
$app['urls'] = function(Application $app) {
  return new UrlRepository($app['database'],'urls',$app['serializer']);
};
$app['generator'] = function(Application $app) {
  return new Generator($app['urls']);
};
$app['authorization'] = function(Application $app) {
  return new BasicAuthorization($app['users'],'prpl.lu');
};

// Create the controllers
$app['api_controller'] = function() {
  return new APIControllerProvider();
};
$app['ui_controller'] = function() {
  return new UIControllerProvider();
};

// Mount the controllers
$app->mount('/api',$app['api_controller']);
$app->mount('/',$app['ui_controller']);

// Run the application
$app->run();