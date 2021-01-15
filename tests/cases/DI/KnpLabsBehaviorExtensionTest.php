<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

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
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\Bridges\SecurityDI\SecurityExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Security\User;
use Nettrine\Extensions\KnpLabs\DI\KnpLabsBehaviorExtension;
use Nettrine\Extensions\KnpLabs\Security\UserCallable;
use Nettrine\Extensions\KnpLabs\Tracy\LoggerCallable;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\GeolocationPointGetter;
use Tests\Fixtures\LocaleGetter;
use Tracy\Bridges\Nette\TracyExtension;

require __DIR__ . '/../../bootstrap.php';

final class KnpLabsBehaviorExtensionTest extends TestCase
{

	public function testDefault(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../../tmp', true);
		$class = $loader->load(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addDependencies([__FILE__]);
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);

		Assert::type(ClassAnalyzer::class, $container->getService('nettrine.extensions.knplabs.classAnalyzer'));
	}

	public function testSimple(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../../tmp', true);
		$class = $loader->load(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig([
				'nettrine.extensions.knplabs' => [
					'blameable' => true,
					'geocodable' => true,
					//'loggable' => true,
					'sluggable' => true,
					'softDeletable' => true,
					'sortable' => true,
					'timestampable' => true,
					'translatable' => true,
					'tree' => true,
				],
			]);
			$compiler->addDependencies([__FILE__]);
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);

		Assert::type(BlameableSubscriber::class, $container->getService('nettrine.extensions.knplabs.blameable'));
		Assert::type(GeocodableSubscriber::class, $container->getService('nettrine.extensions.knplabs.geocodable'));
		//Assert::type(LoggableSubscriber::class, $container->getService('nettrine.extensions.knplabs.loggable'));
		Assert::type(SluggableSubscriber::class, $container->getService('nettrine.extensions.knplabs.sluggable'));
		Assert::type(SoftDeletableSubscriber::class, $container->getService('nettrine.extensions.knplabs.softDeletable'));
		Assert::type(SortableSubscriber::class, $container->getService('nettrine.extensions.knplabs.sortable'));
		Assert::type(TimestampableSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
		Assert::type(TranslatableSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
		Assert::type(TreeSubscriber::class, $container->getService('nettrine.extensions.knplabs.tree'));
	}

	public function testComplex(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../../tmp', true);
		$class = $loader->load(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addExtension('nette.http', new HttpExtension());
			$compiler->addExtension('nette.session', new SessionExtension());
			$compiler->addExtension('nette.security', new SecurityExtension());
			$compiler->addExtension('tracy', new TracyExtension());
			$compiler->addConfig([
				'services' => [
					'userCallable' => UserCallable::class,
					'geolocationPointGetter' => GeolocationPointGetter::class,
					'loggerCallable' => LoggerCallable::class,
				],
				'nettrine.extensions.knplabs' => [
					'blameable' => [
						'trait' => Blameable::class,
						'userCallable' => '@userCallable',
						'userEntity' => User::class,
					],
					'geocodable' => [
						'trait' => Geocodable::class,
						'geolocationCallable' => ['@geolocationPointGetter', 'getPoint'],
					],
					'loggable' => [
						'loggerCallable' => '@loggerCallable',
					],
					'sluggable' => [
						'trait' => Sluggable::class,
					],
					'softDeletable' => [
						'trait' => SoftDeletable::class,
					],
					'sortable' => [
						'trait' => Sortable::class,
					],
					'timestampable' => [
						'trait' => Timestampable::class,
						'dbFieldType' => 'datetimetz',
					],
					'translatable' => [
						'currentLocaleCallable' => [LocaleGetter::class, 'getCurrentLocale'],
						'defaultLocaleCallable' => [LocaleGetter::class, 'getDefaultLocale'],
						'translatableTrait' => Translatable::class,
						'translationTrait' => Translation::class,
						'translatableFetchMode' => 'EXTRA_LAZY',
						'translationFetchMode' => 'EAGER',
					],
					'tree' => [
						'nodeTrait' => Node::class,
					],
				],
			]);
			$compiler->addDependencies([__FILE__]);
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);

		Assert::type(BlameableSubscriber::class, $container->getService('nettrine.extensions.knplabs.blameable'));
		Assert::type(GeocodableSubscriber::class, $container->getService('nettrine.extensions.knplabs.geocodable'));
		Assert::type(LoggableSubscriber::class, $container->getService('nettrine.extensions.knplabs.loggable'));
		Assert::type(SluggableSubscriber::class, $container->getService('nettrine.extensions.knplabs.sluggable'));
		Assert::type(SoftDeletableSubscriber::class, $container->getService('nettrine.extensions.knplabs.softDeletable'));
		Assert::type(SortableSubscriber::class, $container->getService('nettrine.extensions.knplabs.sortable'));
		Assert::type(TimestampableSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
		Assert::type(TranslatableSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
		Assert::type(TreeSubscriber::class, $container->getService('nettrine.extensions.knplabs.tree'));
	}

}

(new KnpLabsBehaviorExtensionTest())->run();
