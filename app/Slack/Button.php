<?php declare(strict_types = 1);

namespace Pd\Monitoring\Slack;

final class Button
{

	private string $name;

	private string $text;

	private string $url;


	public function __construct(
		string $name,
		string $text,
		string $url
	)
	{
		$this->name = $name;
		$this->text = $text;
		$this->url = $url;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getText(): string
	{
		return $this->text;
	}


	public function getUrl(): string
	{
		return $this->url;
	}

}
