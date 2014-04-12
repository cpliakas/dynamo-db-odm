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

### Entities

Create objects that model your entities.

```php

namespace Acme\Entity

use Cpliakas\DynamoDb\ODM\Entity

class Book extends Entity
{
    // The DynanoDB table name
    protected static $table = 'books';

    // The attribute containing the primary key
    protected static $primaryKeyAttribute = 'isbn';

    // Optionally set the $rangeKeyAttribute static if appropriate

    /**
     * Returns the ISBN.
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this['isbn'];
    }
}

```

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
    ->setPrimaryKey('0-1234-5678-9')
    ->setAttribute('title', 'The Book Title')
    ->setAttribute('author', 'Chris Pliakas')
;

// Entity objects also act like arrays.
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
