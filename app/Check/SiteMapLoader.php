<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

final class SiteMapLoader
{

	private string $siteMapUrl;

	/**
	 * @var array<string>|null
	 */
	private ?array $urls = NULL;


	public function __construct(string $siteMapUrl)
	{
		$this->siteMapUrl = $siteMapUrl;

		$urlsData = \file_get_contents($this->siteMapUrl);

		$urlsXml = new \SimpleXMLElement($urlsData);

		foreach ($urlsXml->url as $urlElement) {
			$this->urls[] = (string) $urlElement->loc;
		}
	}


	public function getNextUrl(?string $last): ?string
	{
		if ( ! $last) {
			return \current($this->urls);
		}

		$lastKey = \array_search($last, $this->urls, TRUE);

		return $this->urls[$lastKey + 1] ?? NULL;
	}

}
