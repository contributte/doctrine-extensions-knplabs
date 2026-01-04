<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tester\Utils\Neonkit;
use Knp\DoctrineBehaviors\EventSubscriber\BlameableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\SluggableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\SoftDeletableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TimestampableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TranslatableEventSubscriber;
use Knp\DoctrineBehaviors\EventSubscriber\TreeEventSubscriber;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nettrine\Extensions\KnpLabs\DI\KnpLabsBehaviorExtension;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

// Test: Default configuration - extension loads without configuration
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(Container::class, $container);
});

// Test: Blameable behavior is registered with custom user provider
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				services:
					- Tests\Fixtures\TestEntityManager

				nettrine.extensions.knplabs:
					blameable:
						userCallable: Tests\Fixtures\TestUserProvider
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(BlameableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.blameable'));
});

// Test: Sluggable behavior is registered
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				services:
					- Tests\Fixtures\TestEntityManager

				nettrine.extensions.knplabs:
					sluggable: true
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(SluggableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.sluggable'));
});

// Test: SoftDeletable behavior is registered
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				nettrine.extensions.knplabs:
					softDeletable: true
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(SoftDeletableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.softDeletable'));
});

// Test: Timestampable behavior is registered
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				nettrine.extensions.knplabs:
					timestampable: true
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(TimestampableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
});

// Test: Translatable behavior is registered
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				nettrine.extensions.knplabs:
					translatable: true
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(TranslatableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
});

// Test: Tree behavior is registered
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				nettrine.extensions.knplabs:
					tree: true
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(TreeEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.tree'));
});

// Test: All behaviors are registered together
Toolkit::test(static function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig(Neonkit::load(<<<'NEON'
				services:
					- Tests\Fixtures\TestEntityManager

				nettrine.extensions.knplabs:
					blameable:
						userCallable: Tests\Fixtures\TestUserProvider
					sluggable: true
					softDeletable: true
					timestampable: true
					translatable: true
					tree: true
			NEON
			));
		})
		->withTempDir(Environment::getTestDir())
		->build();

	$container->initialize();
	Assert::type(BlameableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.blameable'));
	Assert::type(SluggableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.sluggable'));
	Assert::type(SoftDeletableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.softDeletable'));
	Assert::type(TimestampableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
	Assert::type(TranslatableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
	Assert::type(TreeEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.tree'));
});
