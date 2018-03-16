<?php
namespace Propel\Model\Url;

interface UrlRepositoryInterface
{
  // Get an URL from the repository
  public function find(int $id): Url;
  
  // Get an URL by short URL
  public function findByShortUrl(string $shortUrl): Url;
  
  // Get an URL by long URL
  public function findByLongUrl(string $longUrl): Url;
  
  // Get all URLs
  public function findAll(): array;
  
  // Create an URL in the repository
  public function create(Url $url);
  
  // Update an URL in the repository
  public function update(Url $url);
  
  // Delete an URL from the repository
  public function delete(Url $url);
}
