<?php
namespace Mand\Generator;

use Mand\Model\UrlRepositoryInterface;

class Generator implements GeneratorInterface
{
  // Constants
  const PATTERN = '0123456789abcdefghijklmnopqrstuvwxyz';

  // Variables
  private $urlRepository;

  // Constructor
  public function __construct(UrlRepositoryInterface $urlRepository)
  {
    $this->urlRepository = $urlRepository;
  }

  // Generates a slug that is available to use
  public function generate(): string
  {
    // Fetch the used slugs from the repository
    $slugs = array_map(function($url) {
      return $url->getId();
    },$this->urlRepository->findAll());

    // Calculate the length of the slug
    $length = max(4,ceil(log10(count($slugs))));

    // Generate a new slug
    do
    {
      $slug = '';
      for ($i = 0; $i < $length; $i ++)
        $slug .= self::PATTERN[mt_rand(0,strlen(self::PATTERN) - 1)];
    } while (in_array($slug,$slugs));

    // Return the slug
    return $slug;
  }
}
