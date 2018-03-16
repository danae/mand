<?php
namespace Propel\Model\User\Authorization;

use Propel\Model\User\UserNotFoundException;
use Propel\Model\User\UserRepositoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class BasicAuthorization implements AuthorizationInterface
{
  // Variables
  private $users;
  private $challenge;
  
  // Constructor
  public function __construct(UserRepositoryInterface $repository, string $realm)
  {
    $this->users = $repository;
    $this->challenge = "Basic realm=\"{$realm}\"";
  }
  
  // Authorizes a request or throws an exception if failed
  public function authorize(Request $request)
  {
    try
    {
      // Check if the headers are valid
      if ($request->getUser() == null || $request->getPassword() == null)
        throw new UnauthorizedHttpException($this->challenge,'The request did not contain a valid Authorization header');
      
      // Check if the user is valid
      $user = $this->users->findByEmail($request->getUser());
      
      // Check if the password is valid
      if (!password_verify($request->getPassword(),$user->getPassword()))
        throw new UnauthorizedHttpException($this->challenge,'The specified password does not match');

      // All checks passed, so return the user
      $app['auth_user'] = $user;
    }
    catch (UserNotFoundException $ex)
    {
      throw new UnauthorizedHttpException($this->challenge,$ex->getMessage(),$ex);
    }
  }
  
  // Optionally authorizes a request
  public function optional(Request $request)
  {
    try
    {
      $this->authorize($request);
    } 
    catch (UnauthorizedHttpException $ex) 
    {
      return;
    }
  }
}
