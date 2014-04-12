<?php

namespace Cpliakas\DynamoDb\ODM;

final class Events
{
    /**
     * This event is thrown prior to an entity being created.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityRequestEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_PRE_CREATE = 'dynamo_db.entity.pre_create';

    /**
     * This event is thrown after an entity has been created.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityResponseEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_POST_CREATE = 'dynamo_db.entity.post_create';

    /**
     * This event is thrown prior to an entity being read from DynamoDB.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityRequestEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_PRE_READ = 'dynamo_db.entity.pre_read';

    /**
     * This event is thrown after to an entity has been read from DynamoDB.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityResponseEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_POST_READ = 'dynamo_db.entity.post_read';

    /**
     * This event is thrown prior to an entity being updated.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityRequestEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_PRE_UPDATE = 'dynamo_db.entity.pre_update';

    /**
     * This event is thrown after an entity has been updated.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityResponseEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_POST_UPDATE = 'dynamo_db.entity.post_update';

    /**
     * This event is thrown prior to an entity being created or updated.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityRequestEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_PRE_SAVE = 'dynamo_db.entity.pre_save';

    /**
     * This event is thrown after an entity has been created or updated.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityResponseEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_POST_SAVE = 'dynamo_db.entity.post_save';

    /**
     * This event is thrown prior to an entity being deleted.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityRequestEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_PRE_DELETE = 'dynamo_db.entity.pre_delete';

    /**
     * This event is thrown after an entity has been deleted.
     *
     * The event listener receives an \Cpliakas\DynamoDb\ODM\Event\EntityResponseEvent
     * instance.
     *
     * @var string
     */
    const ENTITY_POST_DELETE = 'dynamo_db.entity.post_delete';
}
