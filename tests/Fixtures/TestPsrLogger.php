<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Psr\Log\AbstractLogger;

final class TestPsrLogger extends AbstractLogger
{

	/**
	 * @param mixed $level
	 * @param string $message
	 * @param mixed[] $context
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 */
	public function log($level, $message, array $context = []): void
	{
		// No-op for testing
	}

}
