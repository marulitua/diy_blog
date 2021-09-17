<?php

namespace App\Repositories;

use App\Entities\Person;
use Framework\Exceptions\EntityNotFoundException;
use function json_encode;

/**
* Class App\Entities\Repositories
* @author Erwin Pakpahan <erwinmaruli@live.com>
*/
class PersonRepository
{
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function find(int $id): Person
    {
        $stmt = null;
        try {
            $stmt = $this->db->prepare(
                'SELECT id, name, email FROM persons WHERE id = :id'
            );
        }
        catch (\PDOException $e) {
            $stmt = false;
        }

        if (! $stmt) {
            throw new \RuntimeException(
                "unable to prepare statement"
            );
        }

        if (! $stmt->execute(compact('id'))) {
            throw new \RuntimeException(
                "unable to execute statement: {$stmt->queryString}"
            );
        }

        $person = $stmt->fetchObject(Person::class);

        if (! $person) {
            throw new EntityNotFoundException(
                "Cannot found " . Person::class . " with: " . json_encode(compact('id'))
            );
        }


        return $person;
    }
}


