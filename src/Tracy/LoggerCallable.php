<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\Tracy;

use Tracy\ILogger;

final class LoggerCallable
{

	/** @var ILogger */
	private $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}

	public function __invoke(string $message): void
	{
		$this->logger->log($message, ILogger::DEBUG);
	}

}
