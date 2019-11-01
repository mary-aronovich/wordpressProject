<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @var array $reports
 */

foreach ( $reports as $report_name => $report ) : ?>
	<div class="pojo-system-info-section">
		<table class="widefat">
			<thead>
			<tr>
				<th style="width: 15%;"><?php echo $report['label']; ?></th>
				<th></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $report['report'] as $field_name => $field ) :

				if ( in_array( $report_name, array( 'plugins', 'network_plugins', 'mu_plugins' ) ) ) :
					foreach ( $field['value'] as $plugin ) : ?>
						<tr>
							<td><?php
								if ( $plugin['PluginURI'] ) :
									$plugin_name = "<a href='{$plugin['PluginURI']}'>{$plugin['Name']}</a>";
								else :
									$plugin_name = $plugin['Name'];
								endif;

								if ( $plugin['Version'] ) :
									$plugin_name .= ' - ' . $plugin['Version'];
								endif;

								echo $plugin_name; ?></td>
							<td><?php if ( $plugin['Author'] ) :

									if ( $plugin['AuthorURI'] ) :
										$author = "<a href='{$plugin['AuthorURI']}'>{$plugin['Author']}</a>";
									else :
										$author = $plugin['Author'];
									endif;

									printf( __( 'By %s', 'pojo' ), $author );
								endif; ?></td>
							<td></td>
						</tr>
					<?php endforeach;
				else : ?>
					<tr>
						<td><?php echo $field['label']; ?>:</td>
						<td><?php echo $field['value']; ?></td>
						<td><?php if ( ! empty( $field['recommendation'] ) ) :
								echo $field['recommendation'];
							endif; ?></td>
					</tr>
				<?php endif;
			endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endforeach;