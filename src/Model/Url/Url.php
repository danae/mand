<?php
namespace Propel\Model\Url;

use DateTime;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Url implements NormalizableInterface
{
  // Variables
  private $id = NULL;
  private $userId;
  private $shortUrl;
  private $longUrl;
  private $created;
  private $hits = 0;

  // Getters and setters
  public function getId()
  {
    return $this->id;
  }
  public function setId(int $id): self
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
  public function getShortUrl(): string
  {
    return $this->shortUrl;
  }
  public function setShortUrl(string $shortUrl): self
  {
    $this->shortUrl = $shortUrl;
    return $this;
  }
  public function getLongUrl(): string
  {
    return $this->longUrl;
  }
  public function setLongUrl(string $longUrl): self
  {
    $this->longUrl = $longUrl;
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
  public function setHits(int $hits)
  {
    $this->hits = $hits;
    return $this;
  }
  
  // Normalize the URL for a response
  public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
  {
    global $app;
    
    return [
      'type' => 'url',
      
      'id' => $this->getId(),
      'shortUrl' => $this->getShortUrl(),
      'longUrl' => $this->getLongUrl(),
      'created' => $normalizer->normalize($this->getCreated(),$format,$context),
      'hits' => $this->getHits(),
      
      'user' => $normalizer->normalize($app['users']->find($this->getUserId()),$format,$context)
    ];
  }

  // Denormalize this URL
  /*public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
  {
    $this->setId((int)$data['id']);
    $this->setShortUrl($data['shortUrl']);
    $this->setLongUrl($data['longUrl']);
    $this->setCreated($denormalizer->denormalize($data['created'],DateTime::class,$format,$context));
    $this->setHits((int)$data['hits']);
    
    return $this;
  }*/
  
  // Create an URL
  public static function create(string $shortUrl, string $longUrl): self
  {
    return (new self)
      ->setShortUrl($shortUrl)
      ->setLongUrl($longUrl)
      ->setCreated(new DateTime);
  }
}
