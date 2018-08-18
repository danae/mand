<?php
namespace Mand\Generator;

interface GeneratorInterface
{
  // Generates a slug that is available to use
  public function generate(): string;
}
