![](https://heatbadger.now.sh/github/readme/contributte/doctrine-extensions-knplabs/)

<p align=center>
  <a href="https://github.com/contributte/doctrine-extensions-knplabs/actions"><img src="https://badgen.net/github/checks/nettrine/extensions-knplabs/master?cache=300"></a>
  <a href="https://coveralls.io/r/nettrine/extensions-knplabs"><img src="https://img.shields.io/coveralls/nettrine/extensions-knplabs.svg?style=flat-square"></a>
  <a href="https://github.com/phpstan/phpstan"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square"></a>
  <a href="https://packagist.org/packages/nettrine/extensions-knplabs"><img src="https://badgen.net/packagist/dm/nettrine/extensions-knplabs"></a>
  <a href="https://packagist.org/packages/nettrine/extensions-knplabs"><img src="https://badgen.net/packagist/v/nettrine/extensions-knplabs"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/nettrine/extensions-knplabs"><img src="https://badgen.net/packagist/php/nettrine/extensions-knplabs"></a>
  <a href="https://github.com/contributte/doctrine-extensions-knplabs"><img src="https://badgen.net/github/license/contributte/doctrine-extensions-knplabs"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Doctrine ([KnpLabs/DoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors)) behaviors integration for Nette Framework.

## Versions

| State  | Version | Branch   | Nette | PHP     |
|--------|---------|----------|-------|---------|
| dev    | `^0.5`  | `master` | 3.3+  | `>=8.2` |
| stable | `^0.4`  | `master` | 3.3+  | `>=8.2` |

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
    - [Blameable](#blameable)
    - [Loggable](#loggable)
    - [Sluggable](#sluggable)
    - [SoftDeletable](#softdeletable)
    - [Timestampable](#timestampable)
    - [Translatable](#translatable)
    - [Tree](#tree)

## Installation

To install latest version of `nettrine/extensions-knplabs` use [Composer](https://getcomposer.org).

```bash
composer require nettrine/extensions-knplabs
```

Register extension:

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

Enable behavior:

```yaml
nettrine.extensions.knplabs:
    blameable: true
```

Or, if you want to add additional options:

```yaml
nettrine.extensions.knplabs:
    blameable:
        userProvider: App\Security\MyUserProvider  # implements UserProviderInterface
        userEntity: App\Entity\User
        trait: App\Blameable\MyBlameableTrait
```

You may use `Nettrine\Extensions\KnpLabs\Security\UserCallable` for integration with `Nette\Security\User::getId()`.

Use trait in entity:

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

Enable behavior:

```yaml
nettrine.extensions.knplabs:
    loggable:
        logger: @Psr\Log\LoggerInterface  # PSR-3 compatible logger
```

Use trait in entity:

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

Enable behavior:

```yaml
nettrine.extensions.knplabs:
    sluggable: true
```

Or, if you want to add additional options:

```yaml
nettrine.extensions.knplabs:
    sluggable:
        trait: App\Sluggable\MySluggableTrait
```

Use trait in entity and implement abstract methods:

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

Enable behavior:

```yaml
nettrine.extensions.knplabs:
    softDeletable: true
```

Or, if you want to add additional options:

```yaml
nettrine.extensions.knplabs:
    softDeletable:
        trait: App\SoftDeletable\MySoftDeletableTrait
```

Use trait in entity:

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

Enable behavior:

```yaml
nettrine.extensions.knplabs:
    timestampable: true
```

Or, if you want to add additional options:

```yaml
nettrine.extensions.knplabs:
    timestampable:
        dbFieldType: datetime  # datetime | datetimetz | timestamp | timestamptz | ...
        trait: App\Timestampable\MyTimestampableTrait
```

Use trait in entity:

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

Enable behavior:

```yaml
nettrine.extensions.knplabs:
    translatable: true
```

Or, if you want to add additional options:

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

Use trait in entity:

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

And in the translation entity:

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

Enable behavior:

```yaml
nettrine.extensions.knplabs:
    tree: true
```

Or, if you want to add additional options:

```yaml
nettrine.extensions.knplabs:
    tree:
        nodeTrait: App\Tree\MyTreeNodeTrait
```

Use trait in entity and implement interface:

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

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
