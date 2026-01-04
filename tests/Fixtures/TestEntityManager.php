<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * @phpstan-ignore class.implementsDeprecatedInterface
 */
final class TestEntityManager implements EntityManagerInterface
{

	public function getRepository(string $className): never
	{
		throw new \LogicException('Not implemented');
	}

	public function getCache(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function getConnection(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function getExpressionBuilder(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function beginTransaction(): void
	{
		// No-op for testing
	}

	public function transactional(callable $func): never
	{
		throw new \LogicException('Not implemented');
	}

	public function wrapInTransaction(callable $func): never
	{
		throw new \LogicException('Not implemented');
	}

	public function commit(): void
	{
		// No-op for testing
	}

	public function rollback(): void
	{
		// No-op for testing
	}

	public function createQuery(string $dql = ''): never
	{
		throw new \LogicException('Not implemented');
	}

	public function createNativeQuery(string $sql, mixed $rsm): never
	{
		throw new \LogicException('Not implemented');
	}

	public function createQueryBuilder(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function getReference(string $entityName, mixed $id): never
	{
		throw new \LogicException('Not implemented');
	}

	public function close(): void
	{
		// No-op for testing
	}

	public function lock(object $entity, LockMode|int $lockMode, \DateTimeInterface|int|null $lockVersion = null): void
	{
		// No-op for testing
	}

	public function getEventManager(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function getConfiguration(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function isOpen(): bool
	{
		return true;
	}

	public function getUnitOfWork(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function newHydrator(string|int $hydrationMode): AbstractHydrator
	{
		throw new \LogicException('Not implemented');
	}

	public function getProxyFactory(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function getFilters(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function isFiltersStateClean(): bool
	{
		return true;
	}

	public function hasFilters(): bool
	{
		return false;
	}

	public function find(string $className, mixed $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null): ?object
	{
		throw new \LogicException('Not implemented');
	}

	public function persist(object $object): void
	{
		// No-op for testing
	}

	public function remove(object $object): void
	{
		// No-op for testing
	}

	public function refresh(object $object, LockMode|int|null $lockMode = null): void
	{
		// No-op for testing
	}

	public function detach(object $object): void
	{
		// No-op for testing
	}

	public function flush(): void
	{
		// No-op for testing
	}

	public function getClassMetadata(string $className): never
	{
		throw new \LogicException('Not implemented');
	}

	public function getMetadataFactory(): never
	{
		throw new \LogicException('Not implemented');
	}

	public function initializeObject(object $obj): void
	{
		// No-op for testing
	}

	public function isUninitializedObject(mixed $obj): bool
	{
		return false;
	}

	public function contains(object $object): bool
	{
		return false;
	}

	public function clear(): void
	{
		// No-op for testing
	}

}
