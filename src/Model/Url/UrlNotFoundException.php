<?php
namespace Propel\Model\Url;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UrlNotFoundException extends NotFoundHttpException
{
  // Constructor
  public function __construct()
  {
    parent::__construct('The specified URL was not found');
  }
}