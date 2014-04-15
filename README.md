# DynamoDB ODM

A lightweight, no-frills ODM (Object Document Mapper) for DynamoDB.

### Why?

Amazon provides an SDK that connects to DynamoDB. Why would you want to use an
ODM on top of it?

* Allows developers to define their data model in the codebase
* Facilitates readable code by wrapping an OO API around complex data structures
* Adds logical extension points with [Symfony's EventDispatcher component](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
* Optionally enforces [entity integrity](http://en.wikipedia.org/wiki/Entity_integrity)
* Facilitates password hashing, data encryption, random string generation, etc.

## Installation

DynamoDB ODM can be installed with [Composer](http://getcomposer.org)
by adding it as a dependency to your project's composer.json file.

```json
{
    "require": {
        "cpliakas/dynamo-db-odm": "*"
    }
}
```

Please refer to [Composer's documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction)
for more detailed installation and usage instructions.

## Usage

### Defining Entities

Entities are types of documents that are defined in classes that extend
`Cpliakas\DynamoDb\ODM\Entity`. Metadata, such as the table name and hash /
range key attributes, are defined in static properties and accessed through the
static methods defined in `Cpliakas\DynamoDb\ODM\EntityInterface`.

```php

namespace Acme\Entity

use Aws\DynamoDb\Enum\Type;
use Cpliakas\DynamoDb\ODM\Entity

class Book extends Entity
{
    // The DynanoDB table name
    protected static $table = 'books';

    // The attribute containing the hash key
    protected static $hashKeyAttribute = 'isbn';

    // Optionally set the $rangeKeyAttribute static if appropriate

    // Optionally enforce entity integrity
    protected static $enforceEntityIntegrity = true;

    // Optionally map attributes to data types
    protected static $dataTypeMappings = array(
        'isbn' => Type::STRING,
    );

    // Optionally add attribute setters and getters to taste
    public function setIsbn($isbn)
    {
        $this->setAttribute('isbn', $isbn);
        return $this;
    }

    public function getIsbn()
    {
        return $this->setAttribute('isbn');
    }
}
```

*NOTE:* Other ODMs use [annotations](https://github.com/doctrine/annotations)
to define metadata. This pattern can improve DX for applications with a large
number of entities and improve performance when proper caching is implemented.
However, this library intentionally chooses to use statics to define metadata
since it is a lighter-weight solution for the applications this project is
intended to be used in.

### Initializing The Document Manager

The document manager is responsible for instantiating entity classes and reading
/ writing documents to DynamoDB.

```php

require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Cpliakas\DynamoDb\ODM\DocumentManager;

$dynamoDb = DynamoDbClient::factory(array(
    'key'    => '<public-key>',
    'secret' => '<secret-key>',
    'region' => '<aws-region>',
));

$dm = new DocumentManager($dynamoDb);

// Register one or more namespaces that contain entities in order to avoid
// having to pass the fully qualified class names as arguments.
$dm->registerEntityNamesapce('Acme\Entity');

```

### CRUD Operations

Create a document.

```php
// Instantiate the entity object to model the new document. "Book" is the
// entity's class name as defined in the "Defining Entities" example above.
$book = $dm->entityFactory('Book')
    ->setHashKey('0-1234-5678-9')
    ->setAttribute('title', 'The Book Title')
    ->setAttribute('author', 'Chris Pliakas')
;

// Documents can also act like arrays
$book['copyright'] = 2014;

// Save the document
$dm->create($book);
```

Read, update, and delete the entity.

```php

// Read the document
$book = $dm->read('Book', '0-1234-5678-9');

// Update the document
$book['title'] = 'Revised title';
$dm->update($book);

// Delete the document
$dm->delete($book);

```

*NOTE:* Other ODMs use the [unit of work pattern](http://robrich.org/archive/2012/04/18/design-patterns-for-data-persistence-unit-of-work-pattern-and.aspx)
when persisting data to the backend. Due to the nature of DynamoDB and the
desire to keep this library lightweight, we opted not to use this pattern.

### Composite Primary Keys

Pass an array as the primary key parameter when an entity's table uses a hash
and range primary key type.

```php
// Assume that the "Thread" entity's table uses the hash and range primary key
// type containing the forumName and subject attributes.

// Load the document by the hash and range keys
$book = $dm->read('Thread', array('PHP Libraries', 'Using the DynamoDB ODM'));
```

### Scan and Query

```php

use Aws\DynamoDb\Enum\ComparisonOperator;

// Search for books published after 2010 that don't have the title "Do not read me"
$conditions = Conditions::factory()
    ->addCondition('title', 'Do not read me', ComparisonOperator::NE)
    ->addCondition('copyright', 2010, ComparisonOperator::GT)
;

$result = $dm->scan('Book', $conditions);

```
