<?php
namespace Mand\Implementation\MongoDB;

use Database\Database;
use Mand\Model\User;
use Mand\Model\UserRepositoryInterface;
use Symfony\Component\Serializer\Serializer;

class UserRepository implements UserRepositoryInterface
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

  // Get a user from the repository
  public function find(string $id): User
  {
    $data = $this->database->selectOne($this->table,['id' => $id]);
    if ($data === null)
      throw new UserNotFoundException();
    else
      return $this->serializer->denormalize($data,User::class);
  }

  // Get a user by email
  public function findByemail(string $email): User
  {
    $data = $this->database->selectOne($this->table,['email' => $email]);
    if ($data === null)
      throw new UserNotFoundException();
    else
      return $this->serializer->denormalize($data,User::class);
  }

  // Get all users
  public function findAll(): array
  {
    $data = $this->database->select($this->table,[],'modifiedAt desc');
    return array_map(function($row) {
      return $this->serializer->denormalize($row,User::class);
    },$data);
  }

  // Insert a user in the repository
  public function insert(User $user)
  {
    $array = $this->serializer->normalize($user);
    $this->database->insert($this->table,$array);
  }

  // Update a user in the repository
  public function update(User $user)
  {
    $array = $this->serializer->normalize($user);
    $this->database->update($this->table,$array,['id' => $user->getId()]);
  }

  // Delete a user from the repository
  public function delete(User $user)
  {
    $this->database->delete($this->table,['id' => $user->getId()]);
  }
}
