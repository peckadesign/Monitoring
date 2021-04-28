<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls;

class DataGridFactory
{

	public function create(): \Ublaboo\DataGrid\DataGrid
	{
		$grid = new \Ublaboo\DataGrid\DataGrid();

		$grid::$iconPrefix = 'glyphicon glyphicon-';

		$translator = new \Ublaboo\DataGrid\Localization\SimpleTranslator([
			'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
			'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
			'ublaboo_datagrid.here' => 'zde',
			'ublaboo_datagrid.items' => 'Položky',
			'ublaboo_datagrid.all' => 'všechny',
			'ublaboo_datagrid.from' => 'z',
			'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
			'ublaboo_datagrid.group_actions' => 'Hromadné akce',
			'ublaboo_datagrid.show' => 'Zobrazit',
			'ublaboo_datagrid.add' => 'Přidat',
			'ublaboo_datagrid.edit' => 'Upravit',
			'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
			'ublaboo_datagrid.show_default_columns' => 'Zobrazit výchozí sloupce',
			'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
			'ublaboo_datagrid.action' => 'Akce',
			'ublaboo_datagrid.previous' => 'Předchozí',
			'ublaboo_datagrid.next' => 'Další',
			'ublaboo_datagrid.choose' => 'Vyberte',
			'ublaboo_datagrid.choose_input_required' => 'Group action text not allow empty value',
			'ublaboo_datagrid.execute' => 'Provést',
			'ublaboo_datagrid.save' => 'Uložit',
			'ublaboo_datagrid.cancel' => 'Zrušit',
			'ublaboo_datagrid.multiselect_choose' => 'Vybrat',
			'ublaboo_datagrid.multiselect_selected' => '{0} vybráno',
			'ublaboo_datagrid.filter_submit_button' => 'Filter',
			'ublaboo_datagrid.show_filter' => 'Show filter',
			'ublaboo_datagrid.per_page_submit' => 'Změnit',

			'Name' => 'Jméno',
			'Inserted' => 'Vloženo',
		]);
		$grid->setTranslator($translator);

		$grid->setColumnReset(FALSE);

		$grid->setRememberState(FALSE);

		return $grid;
	}

}
