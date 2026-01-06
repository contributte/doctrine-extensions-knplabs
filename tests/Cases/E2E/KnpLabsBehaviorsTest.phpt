<?php declare(strict_types = 1);

namespace Tests\Cases\E2E;

use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\Neonkit;
use Knp\DoctrineBehaviors\EventSubscriber\BlameableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\LoggableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\SluggableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\SoftDeletableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TimestampableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TranslatableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TreeEventSubscriber;
use Nette\DI\Compiler;
use Nettrine\Extensions\KnpLabs\DI\KnpLabsBehaviorExtension;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test: Full configuration with custom settings
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				services:
					- Tests\Fixtures\TestEntityManager

				nettrine.extensions.knplabs:
					blameable:
						userProvider: Tests\Fixtures\TestUserProvider
						userEntity: null
					sluggable:
						trait: Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait
					softDeletable:
						trait: Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait
					timestampable:
						trait: Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait
						dbFieldType: datetime
					translatable:
						localeProvider: Nettrine\Extensions\KnpLabs\Translatable\DefaultLocaleProvider
						translatableTrait: Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait
						translationTrait: Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait
						translatableFetchMode: LAZY
						translationFetchMode: LAZY
					tree:
						nodeTrait: Knp\DoctrineBehaviors\Model\Tree\TreeNodeTrait
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();

	// Verify all event subscribers are registered
	Assert::type(BlameableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.blameable'));
	Assert::type(SluggableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.sluggable'));
	Assert::type(SoftDeletableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.softDeletable'));
	Assert::type(TimestampableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
	Assert::type(TranslatableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
	Assert::type(TreeEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.tree'));
});

// Test: Timestampable with different field types
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				nettrine.extensions.knplabs:
					timestampable:
						dbFieldType: datetimetz
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(TimestampableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
});

// Test: Translatable with custom fetch modes
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				nettrine.extensions.knplabs:
					translatable:
						translatableFetchMode: EXTRA_LAZY
						translationFetchMode: EAGER
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(TranslatableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
});

// Test: Loggable with PSR logger
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				services:
					- Tests\Fixtures\TestPsrLogger

				nettrine.extensions.knplabs:
					loggable:
						logger: @Tests\Fixtures\TestPsrLogger
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(LoggableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.loggable'));
});

// Test: Disabled behaviors are not registered
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				nettrine.extensions.knplabs:
					blameable: false
					sluggable: false
					softDeletable: false
					timestampable: true
					translatable: false
					tree: false
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();

	// Only timestampable should be registered
	Assert::type(TimestampableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));

	// Others should not exist
	Assert::false($container->hasService('nettrine.extensions.knplabs.blameable'));
	Assert::false($container->hasService('nettrine.extensions.knplabs.sluggable'));
	Assert::false($container->hasService('nettrine.extensions.knplabs.softDeletable'));
	Assert::false($container->hasService('nettrine.extensions.knplabs.translatable'));
	Assert::false($container->hasService('nettrine.extensions.knplabs.tree'));
});
