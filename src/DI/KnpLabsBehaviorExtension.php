<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\DI;

use Contributte\DI\Helper\ExtensionDefinitionsHelper;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Geocodable\Geocodable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletable;
use Knp\DoctrineBehaviors\Model\Sortable\Sortable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\ORM\Blameable\BlameableSubscriber;
use Knp\DoctrineBehaviors\ORM\Geocodable\GeocodableSubscriber;
use Knp\DoctrineBehaviors\ORM\Loggable\LoggableSubscriber;
use Knp\DoctrineBehaviors\ORM\Sluggable\SluggableSubscriber;
use Knp\DoctrineBehaviors\ORM\SoftDeletable\SoftDeletableSubscriber;
use Knp\DoctrineBehaviors\ORM\Sortable\SortableSubscriber;
use Knp\DoctrineBehaviors\ORM\Timestampable\TimestampableSubscriber;
use Knp\DoctrineBehaviors\ORM\Translatable\TranslatableSubscriber;
use Knp\DoctrineBehaviors\ORM\Tree\TreeSubscriber;
use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Schema\Elements\AnyOf;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
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
					'trait' => Expect::string(Blameable::class),
					'userCallable' => $this->getCallableSchema()->default(null),
					'userEntity' => Expect::string(null),
				])
			)->default(false)->before($trueToStructureCb),
			'geocodable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(Geocodable::class),
					'geolocationCallable' => $this->getCallableSchema()->default(null),
				])
			)->default(false)->before($trueToStructureCb),
			'loggable' => Expect::anyOf(
				false,
				Expect::structure([
					'loggerCallable' => $this->getCallableSchema()->required(),
				])
			)->default(false),
			'sluggable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(Sluggable::class),
				])
			)->default(false)->before($trueToStructureCb),
			'softDeletable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(SoftDeletable::class),
				])
			)->default(false)->before($trueToStructureCb),
			'sortable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(Sortable::class),
				])
			)->default(false)->before($trueToStructureCb),
			'timestampable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'trait' => Expect::string(Timestampable::class),
					'dbFieldType' => Expect::string('datetime'),
				])
			)->default(false)->before($trueToStructureCb),
			'translatable' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'currentLocaleCallable' => $this->getCallableSchema()->default(null),
					'defaultLocaleCallable' => $this->getCallableSchema()->default(null),
					'translatableFetchMode' => Expect::anyOf('LAZY', 'EAGER', 'EXTRA_LAZY', Expect::int())->default(ClassMetadataInfo::FETCH_LAZY),
					'translationFetchMode' => Expect::anyOf('LAZY', 'EAGER', 'EXTRA_LAZY', Expect::int())->default(ClassMetadataInfo::FETCH_LAZY),
					'translatableTrait' => Expect::string(Translatable::class),
					'translationTrait' => Expect::string(Translation::class),
				])
			)->default(false)->before($trueToStructureCb),
			'tree' => Expect::anyOf(
				Expect::bool(),
				Expect::structure([
					'nodeTrait' => Expect::string(Node::class),
				])
			)->default(false)->before($trueToStructureCb),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;
		$definitionsHelper = new ExtensionDefinitionsHelper($this->compiler);

		$classAnalyzerDefinition = $builder->addDefinition($this->prefix('classAnalyzer'))
			->setFactory(ClassAnalyzer::class)
			->setAutowired(false);
		$isRecursive = true;

		if ($config->blameable !== false) {
			$builder->addDefinition($this->prefix('blameable'))
				->setFactory(BlameableSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$config->blameable->trait,
					$config->blameable->userCallable === null ? null : $definitionsHelper->getCallableFromConfig(
						$config->blameable->userCallable,
						$this->prefix('blameable.callback')
					),
					$config->blameable->userEntity,
				])
				->setAutowired(false);
		}

		if ($config->geocodable !== false) {
			$builder->addDefinition($this->prefix('geocodable'))
				->setFactory(GeocodableSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$config->geocodable->trait,
					$config->geocodable->geolocationCallable === null ? null : $definitionsHelper->getCallableFromConfig(
						$config->geocodable->geolocationCallable,
						$this->prefix('geocodable.callback')
					),
				])
				->setAutowired(false);
		}

		if ($config->loggable !== false) {
			$builder->addDefinition($this->prefix('loggable'))
				->setFactory(LoggableSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$definitionsHelper->getCallableFromConfig(
						$config->loggable->loggerCallable,
						$this->prefix('loggable.callback')
					),
				])
				->setAutowired(false);
		}

		if ($config->sluggable !== false) {
			$builder->addDefinition($this->prefix('sluggable'))
				->setFactory(SluggableSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$config->sluggable->trait,
				])
				->setAutowired(false);
		}

		if ($config->softDeletable !== false) {
			$builder->addDefinition($this->prefix('softDeletable'))
				->setFactory(SoftDeletableSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$config->softDeletable->trait,
				])
				->setAutowired(false);
		}

		if ($config->sortable !== false) {
			$builder->addDefinition($this->prefix('sortable'))
				->setFactory(SortableSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$config->sortable->trait,
				])
				->setAutowired(false);
		}

		if ($config->timestampable !== false) {
			$builder->addDefinition($this->prefix('timestampable'))
				->setFactory(TimestampableSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$config->timestampable->trait,
					$config->timestampable->dbFieldType,
				])
				->setAutowired(false);
		}

		if ($config->translatable !== false) {
			$builder->addDefinition($this->prefix('translatable'))
				->setFactory(TranslatableSubscriber::class, [
					$classAnalyzerDefinition,
					$config->translatable->currentLocaleCallable === null ? null : $definitionsHelper->getCallableFromConfig(
						$config->translatable->currentLocaleCallable,
						$this->prefix('translatable.currentLocaleCallback')
					),
					$config->translatable->defaultLocaleCallable === null ? null : $definitionsHelper->getCallableFromConfig(
						$config->translatable->defaultLocaleCallable,
						$this->prefix('translatable.defaultLocaleCallback')
					),
					$config->translatable->translatableTrait,
					$config->translatable->translationTrait,
					$config->translatable->translatableFetchMode,
					$config->translatable->translationFetchMode,
				])
				->setAutowired(false);
		}

		if ($config->tree !== false) {
			$builder->addDefinition($this->prefix('tree'))
				->setFactory(TreeSubscriber::class, [
					$classAnalyzerDefinition,
					$isRecursive,
					$config->tree->nodeTrait,
				])
				->setAutowired(false);
		}
	}

}
