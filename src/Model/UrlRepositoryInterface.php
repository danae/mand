<?php
namespace Mand\Model;

interface UrlRepositoryInterface
{
  // Get an URL from the repository
  public function find(string $id): ?Url;

  // Get an URL by URL
  public function findByUrl(string $url): ?Url;

  // Get all URLs
  public function findAll(): array;

  // Insert an URL in the repository
  public function insert(Url $url): void;

  // Update an URL in the repository
  public function update(Url $url): void;

  // Delete an URL from the repository
  public function delete(Url $url): void;
}
