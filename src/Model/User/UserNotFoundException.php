<?php
namespace Propel\Model\User;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
  // Constructor
  public function __construct()
  {
    parent::__construct('The specified user was not found');
  }
}