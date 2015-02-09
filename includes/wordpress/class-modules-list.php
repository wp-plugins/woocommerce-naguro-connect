<?php

class Naguro_Modules_List extends WP_List_Table {
	public function get_columns() {
		$columns = array(
			'name' => 'Name',
			'description' => 'Description',
			'active' => 'Active',
		);

		return $columns;
	}

	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

	public function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'name':
				return $item->name;
			case 'description':
				return $item->description;
			case 'active':
				return $item->active ? 'Yes' : 'No';
			default:
				return '';
		}
	}

	public function single_row( $item ) {
		$active_class = ( $item->active ) ? 'active' : '';

		echo '<tr class="'.$active_class.'">';
		echo $this->single_row_columns( $item );
		echo "</tr>\n";
	}
}