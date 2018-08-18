<?php
namespace Mand\Model;

use DateTime;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class User implements NormalizableInterface
{
  // Variables
  private $id = NULL;
  private $email;
  private $password;
  private $created;

  // Management
  public function getId()
  {
    return $this->id;
  }
  public function setId($id): self
  {
    $this->id = $id;
    return $this;
  }
  public function getEmail(): string
  {
    return $this->email;
  }
  public function setEmail(string $email): self
  {
    $this->email = $email;
    return $this;
  }
  public function getPassword(): string
  {
    return $this->password;
  }
  public function setPassword(string $password): self
  {
    $this->password = $password;
    return $this;
  }
  public function getCreated(): DateTime
  {
    return $this->created;
  }
  public function setCreated($created)
  {
    if (is_string($created))
      $this->created = new DateTime($created);
    else if (is_a($created,DateTime::class))
      $this->created = $created;
    else
      throw new InvalidArgumentException('created');

    return $this;
  }

  // Normalize the user for a response
  public function normalize(NormalizerInterface $normalizer, $format = null, array $context = []): array
  {
    return [
      'type' => 'user',

      'email' => $this->getEmail(),
      'created' => $normalizer->normalize($this->getCreated(),$format,$context)
    ];
  }

  // Create a user
  public static function create(string $email, string $password): self
  {
    return (new self)
      ->setEmail($email)
      ->setPassword($password)
      ->setCreated(new DateTime);
  }
}
