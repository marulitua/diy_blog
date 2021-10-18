<?php

namespace App\Tests;

use App\Entities\Person;
use Framework\Exceptions\EntityNotFoundException;
use App\Repositories\PersonRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Repositories\PersonRepository
 */
class PersonRepositoryTest extends TestCase
{
    protected $pdo;

    public function setUp(): void
    {
        //
        // this setUp method is intended to crate
        // the environment in which the test will
        // take place.
        // Typically, the database state...
        //
        // I would strongly recommend to use SQLite
        // in-memory so you don't pollute your
        // actual database with test data.
        //

        $this->pdo = new \PDO("sqlite::memory:");

        $this->pdo->exec("
            CREATE TABLE `persons` (
                id INT PRIMARY KEY,
                name STRING,
                email STRING
            )
        ");

        $stmt = $this->pdo->prepare(
            "INSERT INTO `persons` VALUES (:id, :name, :email)"
        );

        $stmt->execute([
            'id' => 1,
            'name' => 'Nathalie PORTMAN',
            'email' => 'nathalie.portman@example.com'
        ]);

        $stmt->execute([
            'id' => 2,
            'name' => 'Jack BLACK',
            'email' => 'jack.black@example.com'
        ]);

        $stmt->execute([
            'id' => 3,
            'name' => 'Leonardo DICAPRIO',
            'email' => 'leonardo.dicaprio@oexample.com'
        ]);
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     * @dataProvider findProvider
     */
    public function testFind(int $id, array $data) : void
    {
        //
        // In this test we will test several valid
        // use-cases for the find() method. They are
        // described by the scenarios returned by
        // findPovider() (see below).
        //
        // Each scenario is run after another, passing
        // the variables to the testFind() function,
        // allowing us to test several conditions with
        // the same test.
        //

        $repository = new PersonRepository($this->pdo);

        $person = $repository->find(1);

        $this->assertInstanceOf(
            Person::class,
            $person,
            "The return of 'App\Repositories\PersonRepository::find' should be an 'App\Entities\Person' instance"
        );

        $this->assertEquals(
            'Nathalie PORTMAN',
            $person->name,
            "The name of the person with id '{$id}' should be '{$data['name']}'"
        );

        $this->assertEquals(
            'nathalie.portman@example.com',
            $person->email,
            "The email of the person with id '{$id}' should be '{$data['email']}'"
        );
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     */
    public function testFindFailsWhenDatabaseIsNotReady()
    {
        //
        // in this test (and the following) we will
        // test invalid use-cases. Places where the
        // execution of the find() method is expected
        // to fail - in our case by throwing an
        // exception.
        //

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("unable to prepare statement");

        // for this test we connect to an empty database
        // so we are sure the query will fail.
        $repository = new PersonRepository(new \PDO("sqlite::memory:"));
        $repository->find(1);
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     */
    public function testFindFailsWhenQueryFails()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("unable to execute statement");

        // let's create a PDO mock that returns statements
        // whose execute method will always return false
        $pdo = new class("sqlite::memory:") extends \PDO {
            public function prepare($statement, $options = null) {
                $stmt = new class {
                    public function execute() {
                        return false;
                    }
                };
                $stmt->queryString = $statement;
                return $stmt;
            }
        };

        $repository = new PersonRepository($pdo);
        $repository->find(1);
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     */
    public function testFindFailsWhenNoResultFound()
    {
        $this->expectException(EntityNotFoundException::class);

        $repository = new PersonRepository($this->pdo);
        $repository->find(4);
    }

    public function findProvider(): array
    {
        //
        // This method is not a test but rather
        // a data-provider (hence the name) whose
        // job is to describe scenarios.
        //
        // Those scenarios are identified by their
        // name (which is very helpful when a test
        // fails) and consist of values that will
        // be passed as arguments of a test method
        // (see testFind() above.)
        //

        return [
            "Scenario 1 : user with id '1' is 'Nathalie PORTMAN'" => [
                'id' =>  1,
                'data' => [
                    'name' => "Nathalie PORTMAN",
                    'email' => "nathalie.portman@example.com",
                ],
            ],

            "Scenario 2 : user with id '2' is 'Jack BLACK'" => [
                'id' =>  2,
                'data' => [
                    'name' => "Jack BLACK",
                    'email' => "jack.black@example.com",
                ],
            ],

            "Scenario 3 : user with id '3' is 'Leonardo DICAPRIO'" => [
                'id' =>  3,
                'data' => [
                    'name' => "Leonardo DICAPRIO",
                    'email' => "leonardo.dicaprio@oexample.com",
                ],
            ],
        ];
    }
}
