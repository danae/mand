<?php
namespace Mand\Implementation\MongoDB;

use Mand\Model\Url;
use Mand\Model\UrlRepositoryInterface;
use MongoDB\Collection;
use Symfony\Component\Serializer\Serializer;

class UrlRepository implements UrlRepositoryInterface
{
  // Variables
  private $collection;
  private $serializer;

  // Constructor
  public function __construct(Collection $collection, Serializer $serializer)
  {
    $this->collection = $collection;
    $this->serializer = $serializer;
  }

  // Get an URL from the repository
  public function find(string $id): ?Url
  {
    return $this->collection->findOne(['_id' => $id]);
  }

  // Get an URL by URL
  public function findByUrl(string $url): ?Url
  {
    return $this->collection->findOne(['url' => $url]);
  }

  // Get all URLs
  public function findAll($filter = []): array
  {
    $result = $this->collection->find($filter);
    return iterator_to_array($result);
  }

  // Insert an URL in the repository
  public function insert(Url $url): void
  {
    $this->collection->insertOne($url);
  }

  // Update an URL in the repository
  public function update(Url $url): void
  {
    $this->collection->updateOne(['_id' => $url->getId()],$url);
  }

  // Delete an URL from the repository
  public function delete(Url $url): void
  {
    $this->collection->deleteOne(['_id' => $url->getId()]);
  }
}
