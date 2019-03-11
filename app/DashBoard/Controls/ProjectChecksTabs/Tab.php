<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecksTabs;

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

	/**
	 * @var int
	 */
	private $count = 0;


	public function __construct(
		string $title,
		int $status,
		int $count = 0
	) {
		$this->title = $title;
		$this->status = $status;
		$this->count = $count;
	}


	public function getTitle(): string
	{
		return $this->title;
	}


	public function getStatus(): int
	{
		return $this->status;
	}


	public function incrementCount(): void
	{
		$this->count++;
	}


	public function getCount(): int
	{
		return $this->count;
	}

}
