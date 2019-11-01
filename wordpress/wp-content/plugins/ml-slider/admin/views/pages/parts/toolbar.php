<?php if (!defined('ABSPATH')) die('No direct access.');
/** 
 * Using inline-template as there's a flicker before it loads 
 */
?>
<metaslider-toolbar inline-template>
	<div id="ms-toolbar" class="flex flex-col items-center bg-white h-16 shadow-sm lg:sticky z-999" :class="{'shadow-md':scrolling}">
		<div class="container h-full">
			<div class="flex items-center h-full -mx-4">
				<div class="flex items-center h-full py-2 px-4">
					<img style="height:2.3rem;width:2.3rem" width=40 height=40 class="mr-2 rtl:mr-0 rtl:ml-2" src="<?php echo METASLIDER_ADMIN_URL ?>images/metaslider_logo_large.png" alt="MetaSlider">
					<span class="text-2xl font-sans font-thin text-orange leading-none">
						<span class="font-normal">Meta</span>Slider
						<span class="block font-semibold text-sm font-mono text-gray tracking-tight">
							v<?php echo metaslider_pro_is_active() ?  metaslider_pro_version() : $this->version; ?>
						</span>
					</span>
				</div>
				<?php if ($this->slider) : ?>
				<div class="flex-grow h-full px-4">
					<div class="-mx-4 items-center flex h-full">
						<div class="flex items-center flex-grow px-4 h-full">
							<metaslider-switcher></metaslider-switcher>
						</div>
						<div class="px-4 h-full">
							<div class="flex justify-end items-center h-full text-gray">

								<button @click.prevent="addSlide()" id="add-new-slide" class='ms-toolbar-button tipsy-tooltip-bottom-toolbar' title='<?php _e("Add a new slide", "ml-slider") ?>'>
									<i>
										<font-awesome-icon transform="grow-2" icon="plus"></font-awesome-icon>
									</i>
									<span><?php _e("Add Slide", "ml-slider") ?></span>
								</button>

								<button @click.prevent="preview()" id="preview-slideshow" title="<?php echo htmlentities(__('Save & open preview', 'ml-slider')); ?><br><?php echo htmlentities(_x('(alt + p)', 'This is a keyboard shortcut.', 'ml-slider')); ?>" class="ms-toolbar-button tipsy-tooltip-bottom-toolbar" :class="{disabled: $parent.saving}">
									<i>
										<font-awesome-icon transform="grow-2" icon="eye"></font-awesome-icon>
									</i>
									<span><?php _e('Preview', 'ml-slider'); ?></span>
								</button>

								<span class="border-l h-8 mx-2"></span>

								<a class="ms-toolbar-button tipsy-tooltip-bottom-toolbar" title="<?php _e('Read the documentation', 'ml-slider'); ?>" href="https://www.metaslider.com/documentation/" target="_blank">
									<i>
										<font-awesome-icon transform="grow-2" icon="book"></font-awesome-icon>
									</i>
									<span><?php _e('Docs', 'ml-slider'); ?></span>
								</a>

								<span class="border-l h-8 mx-2"></span>

								<a class="ms-toolbar-button tipsy-tooltip-bottom-toolbar" title="<?php _e('Add a new slideshow', 'ml-slider'); ?>" href="<?php echo wp_nonce_url(admin_url("admin-post.php?action=metaslider_create_slider"), "metaslider_create_slider"); ?>">
									<i>
										<font-awesome-icon transform="grow-2" icon="plus-square"></font-awesome-icon>
									</i>
									<span><?php _e('New', 'ml-slider'); ?></span>
								</a>

								<button @click.prevent="duplicate()" title="<?php _e('Duplicate this slideshow', 'ml-slider'); ?>" class="ms-toolbar-button tipsy-tooltip-bottom-toolbar":class="{disabled: duplicating}">
									<i>
										<font-awesome-icon transform="grow-2" icon="clone"></font-awesome-icon>
									</i>
									<span><?php _e('Duplicate', 'ml-slider'); ?></span>
								</button>

								<!-- Pro only add css feature -->
								<?php ob_start(); ?>
								<button @click.prevent="showCSSManagerNotice()" title="<?php esc_attr_e('Add custom CSS', 'ml-slider'); ?><br> - <?php esc_attr_e('press to learn more', 'ml-slider'); ?> -" class="ms-toolbar-button tipsy-tooltip-bottom-toolbar" :class="{disabled:true}">
									<i>
										<font-awesome-icon transform="grow-2" icon="file-code"></font-awesome-icon>
									</i>
									<span><?php _e('Add CSS', 'ml-slider'); ?></span>
								</button>
								<?php echo apply_filters('metaslider_add_css_module', ob_get_clean()); ?>

								<span class="border-l h-8 mx-2"></span>

								<!-- TODO: Create a vue component -->
								<!-- TODO: check what triggers id="ms-save" -->
								<button @click.prevent="save()" title="<?php _e('Save slideshow', 'ml-slider'); ?>" id="ms-save" class="ms-toolbar-button tipsy-tooltip-bottom-toolbar" :class="{disabled: locked}">
									<i v-if="locked">
										<font-awesome-icon transform="grow-2" spin icon="spinner"></font-awesome-icon>
									</i>
									<i v-else>
										<font-awesome-icon transform="grow-2" icon="save"></font-awesome-icon>
									</i>
									<span><?php _e('Save', 'ml-slider'); ?></span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</metaslider-toolbar>
<?php 
if ($this->slider) {
	$nav_opened = filter_var(get_user_option('metaslider_nav_drawer_opened'), FILTER_VALIDATE_BOOLEAN); ?>
	<metaslider-drawer :open="<?php echo $nav_opened ? 'true' : 'false' ?>"></metaslider-drawer>
<?php }
