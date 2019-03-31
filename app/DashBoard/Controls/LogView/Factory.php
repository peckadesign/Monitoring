<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\LogView;

final class Factory
{

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\DataGridFactory
	 */
	private $dataGridFactory;

	/**
	 * @var \Elasticsearch\Client
	 */
	private $elasticsearchClient;


	public function __construct(
		\Pd\Monitoring\DashBoard\Controls\DataGridFactory $dataGridFactory,
		\Elasticsearch\Client $elasticsearchClient
	) {
		$this->dataGridFactory = $dataGridFactory;
		$this->elasticsearchClient = $elasticsearchClient;
	}


	public function create(\Pd\Monitoring\Check\Check $check): \Ublaboo\DataGrid\DataGrid
	{
		$grid = $this->dataGridFactory->create();

		$grid->setPrimaryKey('_id');

		$grid
			->addColumnText('message', 'Záznam')
			->setRenderer(static function (array $row): \Nette\Utils\Html {
				return \Nette\Utils\Html::el('pre', $row['message']);
			})
		;

		$grid
			->addColumnDateTime('datetime', 'Datum')
			->setFormat('j. n. Y H:i:s')
		;

		$rowFactory = static function (array $hit): array {
			return $hit['_source'] + ['_id' => $hit['_id']];
		};
		$dataSource = new \Ublaboo\DatagridElasticsearchDataSource\ElasticsearchDataSource(
			$this->elasticsearchClient,
			'monolog_*',
			'_doc',
			$rowFactory
		);

		$filter = new \Ublaboo\DataGrid\Filter\FilterText($grid, 'context.check', 'context.check', ['context.check']);
		$filter->setValue($check->id);
		$filter->setExactSearch();
		$dataSource->applyFilterText($filter);

		$grid->setDataSource($dataSource);

		$grid->setDefaultSort(['datetime' => 'DESC']);

		return $grid;
	}

}
