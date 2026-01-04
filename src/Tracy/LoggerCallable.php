<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\Tracy;

use Tracy\ILogger;

final class LoggerCallable
{

	public function __construct(
		private ILogger $logger,
	)
	{
	}

	public function __invoke(string $message): void
	{
		$this->logger->log($message, ILogger::DEBUG);
	}

}
