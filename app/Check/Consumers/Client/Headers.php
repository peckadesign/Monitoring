<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers\Client;

/**
 * Výchozí HTTP hlavičky posílané monitoringem na monitorované weby.
 *
 * Některé WAF (např. Cloudflare managed rules) blokují requesty bez hlavičky Accept,
 * proto ji posíláme vždy spolu s User-Agentem.
 */
final class Headers
{

	public const USER_AGENT = 'PeckaMonitoringBot/1.0';

	public const ACCEPT = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';


	/**
	 * Hlavičky ve formátu pro Guzzle (volba "headers").
	 *
	 * @return array<string, string>
	 */
	public static function defaults(): array
	{
		return [
			'User-Agent' => self::USER_AGENT,
			'Accept' => self::ACCEPT,
		];
	}


	/**
	 * Hlavičky jako pole řádků "Název: hodnota" (pro cURL CURLOPT_HTTPHEADER a stream context).
	 *
	 * @return array<int, string>
	 */
	public static function lines(): array
	{
		$lines = [];
		foreach (self::defaults() as $name => $value) {
			$lines[] = $name . ': ' . $value;
		}

		return $lines;
	}

}
