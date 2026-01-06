# Contributte Doctrine Extensions KnpLabs

## Content

Doctrine ([KnpLabs/DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors)) extension for Nette Framework

- [Setup](#setup)
- [Configuration](#configuration)
    - [Blameable](#blameable)
    - [Loggable](#loggable)
    - [Sluggable](#sluggable)
    - [SoftDeletable](#softdeletable)
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

By default all behaviors are disabled, you have to enable them.

Most of the behaviors include a subscriber.
If you use [nettrine/dbal](https://github.com/contributte/doctrine-dbal) then they are configured automatically.
Otherwise you have to add them to the Event manager.

Behaviors `blameable`, `sluggable`, `softDeletable`, `timestampable`, `translatable` and `tree`
have option `trait` (or `*Trait`) which allows you to swap the implementation.

Service references can be provided in two formats:

```yaml
# Service reference (starts with @)
exampleService: @App\MyService

# Class name (will be autowired)
exampleService: App\MyService
```

### [Blameable](https://github.com/KnpLabs/DoctrineBehaviors#blameable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    blameable: true
```

Or, if you want to add additional options

```yaml
nettrine.extensions.knplabs:
    blameable:
        userProvider: App\Security\MyUserProvider  # implements UserProviderInterface
        userEntity: App\Entity\User
        trait: App\Blameable\MyBlameableTrait
```

You may use `Nettrine\Extensions\KnpLabs\Security\UserCallable` for integration with `Nette\Security\User::getId()`.

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;

#[ORM\Entity]
class Article implements BlameableInterface
{
    use BlameableTrait;
}
```

### [Loggable](https://github.com/KnpLabs/DoctrineBehaviors#loggable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    loggable:
        logger: @Psr\Log\LoggerInterface  # PSR-3 compatible logger
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\LoggableInterface;
use Knp\DoctrineBehaviors\Model\Loggable\LoggableTrait;

#[ORM\Entity]
class Article implements LoggableInterface
{
    use LoggableTrait;
}
```

### [Sluggable](https://github.com/KnpLabs/DoctrineBehaviors#sluggable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    sluggable: true
```

Or, if you want to add additional options

```yaml
nettrine.extensions.knplabs:
    sluggable:
        trait: App\Sluggable\MySluggableTrait
```

Use trait in entity and implement abstract methods

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

#[ORM\Entity]
class Article implements SluggableInterface
{
    use SluggableTrait;

    #[ORM\Column]
    private string $title;

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['title'];
    }
}
```

### [SoftDeletable](https://github.com/KnpLabs/DoctrineBehaviors#softdeletable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    softDeletable: true
```

Or, if you want to add additional options

```yaml
nettrine.extensions.knplabs:
    softDeletable:
        trait: App\SoftDeletable\MySoftDeletableTrait
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;

#[ORM\Entity]
class Article implements SoftDeletableInterface
{
    use SoftDeletableTrait;
}
```

### [Timestampable](https://github.com/KnpLabs/DoctrineBehaviors#timestampable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    timestampable: true
```

Or, if you want to add additional options

```yaml
nettrine.extensions.knplabs:
    timestampable:
        dbFieldType: datetime  # datetime | datetimetz | timestamp | timestamptz | ...
        trait: App\Timestampable\MyTimestampableTrait
```

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity]
class Article implements TimestampableInterface
{
    use TimestampableTrait;
}
```

### [Translatable](https://github.com/KnpLabs/DoctrineBehaviors#translatable)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    translatable: true
```

Or, if you want to add additional options

```yaml
nettrine.extensions.knplabs:
    translatable:
        localeProvider: App\Translatable\MyLocaleProvider  # implements LocaleProviderInterface
        translatableFetchMode: LAZY  # LAZY | EAGER | EXTRA_LAZY
        translationFetchMode: LAZY   # LAZY | EAGER | EXTRA_LAZY
        translatableTrait: App\Translatable\MyTranslatableTrait
        translationTrait: App\Translatable\MyTranslationTrait
```

You may use `Nettrine\Extensions\KnpLabs\Translatable\DefaultLocaleProvider` as a base implementation.

Use trait in entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\Entity]
class Article implements TranslatableInterface
{
    use TranslatableTrait;
}
```

And in the translation entity

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\Entity]
class ArticleTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Column]
    private string $title;
}
```

### [Tree](https://github.com/KnpLabs/DoctrineBehaviors#tree)

Enable behavior

```yaml
nettrine.extensions.knplabs:
    tree: true
```

Or, if you want to add additional options

```yaml
nettrine.extensions.knplabs:
    tree:
        nodeTrait: App\Tree\MyTreeNodeTrait
```

Use trait in entity and implement interface

```php
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TreeNodeInterface;
use Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait;

#[ORM\Entity]
class Category implements TreeNodeInterface
{
    use TreeNodeTrait;
}
```
