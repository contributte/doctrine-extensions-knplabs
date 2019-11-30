# Nettrine Extensions KnpLabs

Doctrine ([KnpLabs/DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors)) extension for Nette Framework

## Content

- [Setup](#setup)
- [Configuration](#configuration)
    - [Blameable](#blameable)
    - [Filterable](#filterable)
    - [Geocodable](#geocodable)
    - [Joinable](#joinable)
    - [Loggable](#loggable)
    - [Sluggable](#sluggable)
    - [SoftDeletable](#softdeletable)
    - [Sortable](#sortable)
    - [Timestampable](#timestampable)
    - [Translatable](#translatable)
    - [Tree](#tree)

## Setup

Install package

```bash
composer require nettrine/extensions-knplabs
```

Register extension

```yaml
extensions:
    nettrine.extensions.knplabs: Nettrine\Extensions\KnpLabs\DI\KnpLabsBehaviorExtension
```

## Configuration

By default are all behaviors disabled, you have to enable them.

Most of the behaviors include a subscriber.
If you use [nettrine/dbal](https://github.com/nettrine/dbal) then they are configured automatically.
Otherwise you have to add them to event manager.

Behaviors blameable, geocodable, sluggable, softDeletable, sortable, timestampable, translatable and tree
have option `trait` (or `*Trait`) which allows you to swap behavior implementation.

Behaviors blameable, geocodable, loggable and translatable accepts a callable. You may use all of following syntaxes:

```yaml
# Static method call (or any other valid callable, like 'someFunction')
exampleCallable: [StaticClass, 'calledMethod']

# Service method call
exampleCallable: [@service, 'calledMethod'],

# Reference to service which implements __invoke()
exampleCallable: @serviceWhichImplements__invoke()

# Register and use new service which implements __invoke(), like you would do in 'services' config section
exampleCallable:
    factory: Your\Special\Service
```

### [Blameable](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#blameable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    blameable: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    blameable:
        userCallable: callable ## returns object specified by userEntity or object which implements __toString() or string
        userEntity: Your\User\Entity
        trait: YourBlameableTrait
```

You may use `Nettrine\Extensions\KnpLabs\Security\UserCallable` for `Nette\Security\User::getId()`

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class BlameableEntity
{

	use Behaviors\Blameable\Blameable;

}
```

### [Filterable](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#filterable)

Use trait in repository and implement abstract methods

```php
use Doctrine\ORM\EntityRepository;
use Knp\DoctrineBehaviors\ORM as Behaviors;

final class FilterableRepository extends EntityRepository
{

    use Behaviors\Filterable\FilterableRepository;

    public function getLikeFilterColumns(): array
    {
        // TODO: Implement getLikeFilterColumns() method.
    }

    public function getILikeFilterColumns(): array
    {
        // TODO: Implement getILikeFilterColumns() method.
    }

    public function getEqualFilterColumns(): array
    {
        // TODO: Implement getEqualFilterColumns() method.
    }

    public function getInFilterColumns(): array
    {
        // TODO: Implement getInFilterColumns() method.
    }

}
```

### [Geocodable](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#geocodable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    geocodable: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    geocodable:
        geolocationCallable: callable # accepts entity, returns Point|false
        trait: YourGeocodableTrait
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class GeocodableEntity
{

	use Behaviors\Geocodable\Geocodable;

}
```

Use trait in repository

```php
use Doctrine\ORM\EntityRepository;
use Knp\DoctrineBehaviors\ORM as Behaviors;

final class GeocodableRepository extends EntityRepository
{

    use Behaviors\Geocodable\GeocodableRepository;

}
```

### Joinable

Use trait in repository

```php
use Doctrine\ORM\EntityRepository;
use Knp\DoctrineBehaviors\ORM as Behaviors;

final class JoinableRepository extends EntityRepository
{

    use Behaviors\Joinable\JoinableRepository;

}
```

### [Loggable](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#loggable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    loggable:
        loggerCallable: callable # accepts message
```

You may use `Knp\DoctrineBehaviors\ORM\Loggable\LoggerCallable` for PSR-3 logger or `Nettrine\Extensions\KnpLabs\Tracy\LoggerCallable` for Tracy logger.

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class LoggableEntity
{

	use Behaviors\Loggable\Loggable;

}
```

### [Sluggable](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#sluggable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    sluggable: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    sluggable:
        trait: YourSluggableTrait
```

Use trait in entity and implement abstract methods

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class SluggableEntity
{

	use Behaviors\Sluggable\Sluggable;

    public function getSluggableFields(): array
    {
        // TODO: Implement getSluggableFields() method.
    }

}
```

### [SoftDeletable](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#softDeletable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    softDeletable: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    softDeletable:
        trait: YourSoftDeletableTrait
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class SoftDeletableEntity
{

	use Behaviors\SoftDeletable\SoftDeletable;

}
```

### Sortable

Enable behavior

```yaml
nettrine.extensions.knplabs:
    sortable: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    sortable:
        trait: YourSortableTrait
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class SortableEntity
{

	use Behaviors\Sortable\Sortable;

}
```

Use trait in repository

```php
use Doctrine\ORM\EntityRepository;
use Knp\DoctrineBehaviors\ORM as Behaviors;

final class SortableRepository extends EntityRepository
{

    use Behaviors\Sortable\SortableRepository;

}
```

### [Timestampable](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#timestampable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    timestampable: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    timestampable:
        dbFieldType: datetime | datetimetz | timestamp | timestamptz | ... # default: datetime
        trait: YourTimestampableTrait
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class TimestampableEntity
{

	use Behaviors\Timestampable\Timestampable;

}
```

### [Translatable](#https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#translatable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    translatable: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    translatable:
        currentLocaleCallable: callable # returns current locale or null
        defaultLocaleCallable: callable # returns default locale or null
        translatableFetchMode: LAZY | EAGER | EXTRA_LAZY # default: LAZY
        translationFetchMode: LAZY | EAGER | EXTRA_LAZY # default: LAZY
        translatableTrait: YourTranslatableTrait
        translationTrait: YourTranslationTrait
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class TranslatableEntity
{

	use Behaviors\Translatable\Translatable;

}
```

### [Tree](https://github.com/KnpLabs/DoctrineBehaviors/tree/v1#tree)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    tree: true
```

Or, if you want add additional options

```yaml
nettrine.extensions.knplabs:
    tree:
        nodeTrait: YourTreeNodeTrait
```

Use trait in entity and implement interface

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as Behaviors;

class TreeEntity implements Behaviors\Tree\NodeInterface
{

	use Behaviors\Tree\Node;

}
```

Use trait in repository

```php
use Doctrine\ORM\EntityRepository;
use Knp\DoctrineBehaviors\ORM as Behaviors;

final class TreeRepository extends EntityRepository
{

    use Behaviors\Tree\Tree;

}
```
