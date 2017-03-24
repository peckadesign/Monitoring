<?php declare(strict_types=1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecks;

class Tab
{

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var int
	 */
	private $status;


	public function __construct(
		string $title,
		int $status
	) {
		$this->title = $title;
		$this->status = $status;
	}


	public function getTitle(): string
	{
		return $this->title;
	}


	public function getStatus(): int
	{
		return $this->status;
	}

}
