<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Psr\Log\AbstractLogger;

final class TestPsrLogger extends AbstractLogger
{

	/**
	 * @param mixed[] $context
	 */
	public function log(mixed $level, \Stringable|string $message, array $context = []): void
	{
		// No-op for testing
	}

}
