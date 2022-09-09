<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Doctrine\ORM\EntityManagerInterface;
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
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\Bridges\SecurityDI\SecurityExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\DI\Definitions\Statement;
use Nette\Security\User;
use Nettrine\Extensions\KnpLabs\DI\KnpLabsBehaviorExtension;
use Nettrine\Extensions\KnpLabs\Security\UserCallable;
use Nettrine\Extensions\KnpLabs\Translatable\DefaultLocaleProvider;
use Tester\Assert;
use Tester\TestCase;
use Tests\Fixtures\GeolocationPointGetter;
use Tracy\Bridges\Nette\TracyExtension;
use Tracy\Bridges\Psr\TracyToPsrLoggerAdapter;

require __DIR__ . '/../../bootstrap.php';

class UserStorageStub implements \Nette\Security\UserStorage
{

	public function saveAuthentication(\Nette\Security\IIdentity $identity): void
	{

	}


	public function clearAuthentication(bool $clearIdentity): void
	{

	}


	public function getState(): array
	{

	}


	public function setExpiration(?string $expire, bool $clearIdentity): void
	{

	}

}

class EntityManagerStub extends \Doctrine\ORM\EntityManager
{
	public function __construct()
	{
	}
}

final class KnpLabsBehaviorExtensionTest extends TestCase
{

	public function testDefault(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../../tmp', true);
		$class = $loader->load(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig([
				'services' => [
					'entityManager' => [
						'type' => EntityManagerInterface::class,
						'factory' => new Statement(EntityManagerStub::class),
					],
				],
			]);
			$compiler->addDependencies([__FILE__]);
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);
	}

	public function testSimple(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../../tmp', true);
		$class = $loader->load(static function (Compiler $compiler): void {
			$compiler->addExtension('nettrine.extensions.knplabs', new KnpLabsBehaviorExtension());
			$compiler->addConfig([
				'services' => [
					'user' => [
						'factory' => new Statement(User::class, [
							'storage' => new UserStorageStub(),
						]),
					],
					'entityManager' => [
						'type' => EntityManagerInterface::class,
						'factory' => new Statement(EntityManagerStub::class),
					],
				],
				'nettrine.extensions.knplabs' => [
					'blameable' => true,
					//'loggable' => true,
					'sluggable' => true,
					'softDeletable' => true,
					'timestampable' => true,
					'translatable' => true,
					'tree' => true,
				],
			]);
			$compiler->addDependencies([__FILE__]);
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);

		Assert::type(BlameableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.blameable'));
		//Assert::type(LoggableSubscriber::class, $container->getService('nettrine.extensions.knplabs.loggable'));
		Assert::type(SluggableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.sluggable'));
		Assert::type(SoftDeletableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.softDeletable'));
		Assert::type(TimestampableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
		Assert::type(TranslatableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
		Assert::type(TreeEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.tree'));
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
					'loggerCallable' => TracyToPsrLoggerAdapter::class,
					'entityManager' => [
						'type' => EntityManagerInterface::class,
						'factory' => new Statement(EntityManagerStub::class),
					],
				],
				'nettrine.extensions.knplabs' => [
					'blameable' => [
						'trait' => BlameableTrait::class,
						'userCallable' => '@userCallable',
						'userEntity' => User::class,
					],
					'loggable' => [
						'logger' => '@loggerCallable',
					],
					'sluggable' => [
						'trait' => SluggableTrait::class,
					],
					'softDeletable' => [
						'trait' => SoftDeletableTrait::class,
					],
					'timestampable' => [
						'trait' => TimestampableTrait::class,
						'dbFieldType' => 'datetimetz',
					],
					'translatable' => [
						'localeProvider' => DefaultLocaleProvider::class,
						'translatableTrait' => TranslatableTrait::class,
						'translationTrait' => TranslationTrait::class,
						'translatableFetchMode' => 'EXTRA_LAZY',
						'translationFetchMode' => 'EAGER',
					],
					'tree' => [
						'nodeTrait' => TreeNodeTrait::class,
					],
				],
			]);
			$compiler->addDependencies([__FILE__]);
		}, __METHOD__);

		$container = new $class();
		assert($container instanceof Container);

		Assert::type(BlameableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.blameable'));
		Assert::type(LoggableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.loggable'));
		Assert::type(SluggableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.sluggable'));
		Assert::type(SoftDeletableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.softDeletable'));
		Assert::type(TimestampableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.timestampable'));
		Assert::type(TranslatableEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.translatable'));
		Assert::type(TreeEventSubscriber::class, $container->getService('nettrine.extensions.knplabs.tree'));
	}

}

(new KnpLabsBehaviorExtensionTest())->run();
