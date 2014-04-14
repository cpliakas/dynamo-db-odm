# DynamoDB ODM

A light-weight, no-frills ODM for DynamoDB.

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

Entities are defined through classes that extend `Cpliakas\DynamoDb\ODM\Entity`.
Metadata, such as the table name and primary key attributes, are defined in
static properties and accessed through the static methods defined in
`Cpliakas\DynamoDb\ODM\EntityInterface`.

```php

namespace Acme\Entity

use Cpliakas\DynamoDb\ODM\Entity

class Book extends Entity
{
    // The DynanoDB table name
    protected static $table = 'books';

    // The attribute containing the primary key
    protected static $hashAttribute = 'isbn';

    // Optionally set the $rangeAttribute static if appropriate

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

*NOTE:* Other O*Ms use [annotations](https://github.com/doctrine/annotations)
to define metadata. This pattern can improve DX for applications with a large
number of entities and improve performance when proper caching is implemented,
however this library intentionally chooses to use statics to define metadata
since it is a lighter-weight solution for the applications this library is
intended to be used in.

### CRUD operations

Instantiate the Document manager and create an entity.

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
$dm->registerEntityNamesapce('Acme\Entity');

// Instantiate the entity object.
$book = $dm->entityFactory('Book')
    ->setHash('0-1234-5678-9')
    ->setAttribute('title', 'The Book Title')
    ->setAttribute('author', 'Chris Pliakas')
;

// Entity objects can also act like arrays.
$book['copyright'] = '2014';

// Save the entity.
$dm->create($book);

```

Load, modify, and delete the entity.

```php

// Load the entity.
$book = $dm->read('Book', '0-1234-5678-9');

// Update the entity.
$book['title'] = 'Revised title';
$dm->update($book);

// Delete the entity.
$dm->delete($book);

```

### Composite Primary Keys

Pass an array as the primary key parameter when an entity's table uses a hash
and range primary key type.

```php
// Assume the "Thread" entity's table uses the hash and range primary key type
// for the forum name and subject.

// Load the entity from the primary key's hash and range attributes.
$book = $dm->read('Thread', array('PHP Libraries', 'Using the DynamoDB ODM'));
```