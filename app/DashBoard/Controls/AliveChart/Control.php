<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AliveChart;

final class Control extends \Nette\Application\UI\Control
{

	private \Pd\Monitoring\Check\AliveCheck $check;

	private \Pd\Monitoring\Elasticsearch\Queries\AverageTimeoutQuery $averageTimeoutQuery;

	private \Nette\Caching\Cache $cache;


	public function __construct(
		\Pd\Monitoring\Check\AliveCheck $aliveCheck,
		\Pd\Monitoring\Elasticsearch\Queries\AverageTimeoutQuery $averageTimeoutQuery,
		\Nette\Caching\Cache $cache
	)
	{
		$this->check = $aliveCheck;
		$this->averageTimeoutQuery = $averageTimeoutQuery;
		$this->cache = $cache;
	}


	public function render(): void
	{
		$cb = function (&$dp): array
		{
			$data = [['Den', 'Dnes', 'Včera', 'Týden']];
			foreach ($this->averageTimeoutQuery->query($this->check->id, 0) as $date => $timeout) {
				$month = new \DateTime($date);
				$hour = $month->format('H:00');
				$data[$hour] = [$hour, (int) $timeout];
			}

			foreach ($this->averageTimeoutQuery->query($this->check->id, 1) as $date => $timeout) {
				$month = new \DateTime($date);
				$hour = $month->format('H:00');
				$data[$hour][] = (int) $timeout;
			}

			foreach ($this->averageTimeoutQuery->query($this->check->id, 7) as $date => $timeout) {
				$month = new \DateTime($date);
				$hour = $month->format('H:00');
				$data[$hour][] = (int) $timeout;
			}

			$dp[\Nette\Caching\Cache::EXPIRE] = '+15 minutes';

			return \array_values($data);
		};

		$data = $this->cache->load('aliveChart' . $this->check->id, $cb);

		$element = [
			'id' => \Nette\Utils\Random::generate(),
			'data-plot-chart' => 'AreaChart',
			'data-plot-enable' => \Nette\Utils\Json::encode(TRUE),
			'data-plot-data' => \Nette\Utils\Json::encode($data),
			'data-plot-options' => \Nette\Utils\Json::encode(['title' => 'Průměrná odezva', 'backgroundColor' => 'transparent', 'legend' => 'top']),
		];

		$chartElement = \Nette\Utils\Html::el('div', $element);

		echo $chartElement;
	}

}
