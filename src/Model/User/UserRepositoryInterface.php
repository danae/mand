<?php
namespace Propel\Model\User;

interface UserRepositoryInterface
{
  // Get a user from the repository
  public function find(string $name): User;
  
  // Get a user by email
  public function findByEmail(string $email): User;
  
  // Get all users
  public function findAll(): array;
  
  // Create a user in the repository
  public function create(User $user);
  
  // Update a user in the repository
  public function update(User $user);
  
  // Deletes a user from the repository
  public function delete(User $user);
}
