<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\Translatable;

use Knp\DoctrineBehaviors\Contract\Provider\LocaleProviderInterface;

class DefaultLocaleProvider implements LocaleProviderInterface
{

	public function provideCurrentLocale(): ?string
	{
		return null;
	}

	public function provideFallbackLocale(): ?string
	{
		return null;
	}

}
