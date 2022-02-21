<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\DI;

use Contributte\DI\Helper\ExtensionDefinitionsHelper;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait;
use Knp\DoctrineBehaviors\EventSubscriber\BlameableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\LoggableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\SluggableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\SoftDeletableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TimestampableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TranslatableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TreeEventSubscriber;
use Knp\DoctrineBehaviors\Repository\DefaultSluggableRepository;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Elements\AnyOf;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nettrine\Extensions\KnpLabs\Security\UserCallable;
use Nettrine\Extensions\KnpLabs\Translatable\DefaultLocaleProvider;
use stdClass;

/**
 * @property-read stdClass $config
 */
final class KnpLabsBehaviorExtension extends CompilerExtension
{

	private function getCallableSchema(): AnyOf
	{
		return Expect::anyOf(Expect::string(), Expect::array(), Expect::type(Statement::class));
	}

	public function getConfigSchema(): Schema
	{
		$trueToStructureCb = static function ($value) {
			if ($value === true) {
				return [];
			}

			return $value;
		};

		return Expect::structure([
			'blameable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(BlameableTrait::class),
					'userCallable' => $this->getCallableSchema()->default(UserCallable::class),
					'userEntity' => Expect::string(null),
				])
			)->default(false)->before($trueToStructureCb),
			'loggable' => Expect::anyOf(
				false,
				Expect::structure([
					'logger' => $this->getCallableSchema()->required(),
				])
			)->default(false),
			'sluggable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(SluggableTrait::class),
				])
			)->default(false)->before($trueToStructureCb),
			'softDeletable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(SoftDeletableTrait::class),
				])
			)->default(false)->before($trueToStructureCb),
			'timestampable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(TimestampableTrait::class),
					'dbFieldType' => Expect::string('datetime'),
				])
			)->default(false)->before($trueToStructureCb),
			'translatable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'localeProvider' => $this->getCallableSchema()->default(DefaultLocaleProvider::class),
					'translatableFetchMode' => Expect::anyOf('LAZY', 'EAGER', 'EXTRA_LAZY', Expect::int())->default('LAZY'),
					'translationFetchMode' => Expect::anyOf('LAZY', 'EAGER', 'EXTRA_LAZY', Expect::int())->default('LAZY'),
					'translatableTrait' => Expect::string(TranslatableTrait::class),
					'translationTrait' => Expect::string(TranslationTrait::class),
				])
			)->default(false)->before($trueToStructureCb),
			'tree' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'nodeTrait' => Expect::string(TreeNodeTrait::class),
				])
			)->default(false)->before($trueToStructureCb),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;
		$definitionsHelper = new ExtensionDefinitionsHelper($this->compiler);

		if ($config->blameable !== false) {
			$builder->addDefinition($this->prefix('blameable'))
				->setType(BlameableEventSubscriber::class)
				->setArguments([
					'userProvider' => $definitionsHelper->getCallableFromConfig(
						$config->blameable->userCallable,
						$this->prefix('blameable.callback')
					),
					'blameableUserEntity' => $config->blameable->userEntity,
				])
				->setAutowired(false);
		}

		if ($config->loggable !== false) {
			$builder->addDefinition($this->prefix('loggable'))
				->setType(LoggableEventSubscriber::class)
				->setArguments([
					$config->loggable->logger,
				])
				->setAutowired(false);
		}

		if ($config->sluggable !== false) {
			$sluggableRepository = $builder->addDefinition($this->prefix('suggable.repository'))
				->setType(DefaultSluggableRepository::class)
				->setAutowired(false);

			$builder->addDefinition($this->prefix('sluggable'))
				->setType(SluggableEventSubscriber::class)
				->setArguments([
					'defaultSluggableRepository' => $sluggableRepository
				])
				->setAutowired(false);
		}

		if ($config->softDeletable !== false) {
			$builder->addDefinition($this->prefix('softDeletable'))
				->setType(SoftDeletableEventSubscriber::class)
				->setAutowired(false);
		}

		if ($config->timestampable !== false) {
			$builder->addDefinition($this->prefix('timestampable'))
				->setType(TimestampableEventSubscriber::class)
				->setArguments([
					$config->timestampable->dbFieldType,
				])
				->setAutowired(false);
		}

		if ($config->translatable !== false) {
			$localeProvider = $builder->addDefinition($this->prefix('translatable.localeProvider'))
				->setType($config->translatable->localeProvider)
				->setAutowired(false);

			$builder->addDefinition($this->prefix('translatable'))
				->setType(TranslatableEventSubscriber::class)
				->setArguments([
					$localeProvider,
					$config->translatable->translatableFetchMode,
					$config->translatable->translationFetchMode,
				])
				->setAutowired(false);
		}

		if ($config->tree === false) {
			return;
		}

		$builder->addDefinition($this->prefix('tree'))
			->setType(TreeEventSubscriber::class)
			->setAutowired(false);
	}

}
