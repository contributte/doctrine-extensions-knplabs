<?php declare(strict_types = 1);

namespace Tests\Fixtures;

final class LocaleGetter
{

	public static function getCurrentLocale(): ?string
	{
		return null;
	}

	public static function getDefaultLocale(): ?string
	{
		return 'en';
	}

}
