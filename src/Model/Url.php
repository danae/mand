<?php
namespace Mand\Model;

use DateTime;
use InvalidArgumentException;
use MongoDB\BSON\Persistable;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Url implements Persistable, NormalizableInterface
{
  // Variables
  private $id;
  private $url;
  private $created;
  private $hits = 0;
  private $userId = 1;

  // Getters and setters
  public function getId(): ?string
  {
    return $this->id;
  }
  public function setId(string $id): self
  {
    $this->id = $id;
    return $this;
  }
  public function getUserId(): int
  {
    return $this->userId;
  }
  public function setUserId(int $userId): self
  {
    $this->userId = $userId;
    return $this;
  }
  public function getUrl(): string
  {
    return $this->url;
  }
  public function setUrl(string $url): self
  {
    $this->url = $url;
    return $this;
  }
  public function getCreated(): DateTime
  {
    return $this->created;
  }
  public function setCreated($created): self
  {
    if (is_string($created))
      $this->created = new DateTime($created);
    else if (is_a($created,DateTime::class))
      $this->created = $created;
    else
      throw new InvalidArgumentException('created');

    return $this;
  }
  public function getHits(): int
  {
    return $this->hits;
  }
  public function setHits(int $hits): self
  {
    $this->hits = $hits;
    return $this;
  }

  // Serialize to BSON
  public function bsonSerialize()
  {
    return [
      '_id' => $this->getId(),
      'url' => $this->getUrl(),
      'created' => $this->getCreated()->format(DateTime::RFC3339),
      'hits' => $this->getHits(),
      'user_id' => $this->getUserId()
    ];
  }

  // Unserialize from BSON
  public function bsonUnserialize(array $data): self
  {
    $this->setId($data['_id']);
    $this->setUrl($data['url']);
    $this->setCreated($data['created']);
    $this->setHits($data['hits']);
    $this->setUserId($data['user_id']);
    return $this;
  }

  // Normalize the URL for a response
  public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
  {
    global $app;

    return [
      'type' => 'url',

      'id' => $this->getId(),
      'url' => $this->getUrl(),
      'created' => $normalizer->normalize($this->getCreated(),$format,$context),
      'hits' => $this->getHits(),

      //'user' => $normalizer->normalize($app['users']->find($this->getUserId()),$format,$context)
    ];
  }

  // Create an URL
  public static function create(string $alias, string $url): self
  {
    return (new self)
      ->setId($alias)
      ->setUrl($url)
      ->setCreated(new DateTime);
  }
}
