<?php
namespace Propel\Model\Url;

use Database\Database;
use Symfony\Component\Serializer\Serializer;

class UrlRepository implements UrlRepositoryInterface
{
  // Variables
  private $database;
  private $table;
  private $serializer;
  
  // Constructor
  public function __construct(Database $database, string $table, Serializer $serializer)
  {
    $this->database = $database;
    $this->table = $table;
    $this->serializer = $serializer;
  }
  
  // Get an URL from the repository
  public function find(int $id): Url
  {
    $data = $this->database->selectOne($this->table,['id' => $id]);
    if ($data === null)
      throw new UrlNotFoundException();
    else
      return $this->serializer->denormalize($data,Url::class);
  }
  
  // Get an URL by short URL
  public function findByShortUrl(string $shortUrl): Url
  {
    $data = $this->database->selectOne($this->table,['shortUrl' => $shortUrl]);
    if ($data === null)
      throw new UrlNotFoundException();
    else
      return $this->serializer->denormalize($data,Url::class);
  }
  
  // Get an URL by long URL
  public function findByLongUrl(string $longUrl): Url
  {
    $data = $this->database->selectOne($this->table,['longUrl' => $longUrl]);
    if ($data === null)
      throw new UrlNotFoundException();
    else
      return $this->serializer->denormalize($data,Url::class);
  }
    
  // Get all URLs
  public function findAll($where = []): array
  {
    $data = $this->database->select($this->table,$where,'created asc');
    return array_map(function($row) {
      return $this->serializer->denormalize($row,Url::class);
    },$data);
  }
  
  // Create an URL in the repository
  public function create(Url $url)
  {
    $array = $this->serializer->normalize($url);
    $this->database->insert($this->table,$array);
  }
  
  // Update an URL in the repository
  public function update(Url $url)
  {
    $array = $this->serializer->normalize($url);
    $this->database->update($this->table,$array,['id' => $url->getId()]);
  }
  
  // Delete an URL from the repository
  public function delete(Url $url)
  {
    $this->database->delete($this->table,['id' => $url->getId()]);
  }
}
