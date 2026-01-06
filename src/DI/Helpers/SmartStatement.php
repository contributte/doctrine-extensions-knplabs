<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\DI\Helpers;

use Nette\DI\Definitions\Statement;
use Nettrine\Extensions\KnpLabs\Exceptions\LogicalException;

final class SmartStatement
{

	public static function from(mixed $service): Statement
	{
		if (is_string($service)) {
			return new Statement($service);
		}

		if ($service instanceof Statement) {
			return $service;
		}

		throw new LogicalException('Unsupported type of service. Expected string or Statement.');
	}

}
