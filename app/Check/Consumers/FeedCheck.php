<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check\Consumers;

class FeedCheck extends Check
{

	/**
	 * @param \Pd\Monitoring\Check\Check|\Pd\Monitoring\Check\FeedCheck $check
	 * @return bool
	 */
	protected function doHardJob(\Pd\Monitoring\Check\Check $check): bool
	{
		$ch = curl_init($check->url);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);    // we want headers
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);    // we don't need body
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$output = curl_exec($ch);

		$check->lastModified = NULL;
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
			$check->lastSize = NULL;
		} else {
			$check->lastSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

			foreach (explode("\n", $output) as $line) {
				if (stripos($line, 'last-modified:') !== FALSE) {
					$check->lastModified = new \DateTimeImmutable(trim(str_ireplace('last-modified:', '', $line)));
					break;
				}
			}
		}
		curl_close($ch);

		return $check->lastSize ? TRUE : FALSE;
	}


	protected function getCheckType(): int
	{
		return \Pd\Monitoring\Check\ICheck::TYPE_FEED;
	}
}
