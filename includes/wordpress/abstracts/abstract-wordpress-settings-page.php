<?php

abstract class Abstract_Naguro_WordPress_Settings_Page {
	protected function output_tabs( $active_tab = 'dashboard' ) {
		do_action( 'naguro_before_output_tabs' );

		?>
		<div class="icon32 icon32-woocommerce-naguro-settings" id="icon-woocommerce-naguro"><br /></div>
		<h2 class="nav-tab-wrapper">

			<?php
			$base_classes = 'nav-tab';
			$admin_url = admin_url( 'admin.php?page=woocommerce-naguro' );

			$tabs = array(
				'dashboard' => array(
					'url' => $admin_url . '&tab=dashboard',
					'label' => 'Dashboard',
					'classes' => $base_classes,
				),
				'modules' => array(
					'url' => $admin_url . '&tab=modules',
					'label' => 'Modules',
					'classes' => $base_classes,
				),
				'settings' => array(
					'url' => $admin_url . '&tab=settings',
					'label' => 'Settings',
					'classes' => $base_classes,
				),
			);

			foreach ( $tabs as $key => $tab ) {
				$tab['classes'] = ( $key === $active_tab ) ? $tab['classes'] . ' nav-tab-active' : $tab['classes'];
				echo '<a href="'.$tab['url'].'" class="'.$tab['classes'].'">'.$tab['label'].'</a>';
			}
			?>
		</h2>
		<?php

		do_action( 'naguro_after_output_tabs' );
	}
}