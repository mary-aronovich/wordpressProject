<?php if (!defined('ABSPATH')) die('No direct access.');
/** 
 * The shortcode module
 */
?>
<metaslider-shortcode inline-template>
	<div class="shadow-sm bg-white mb-6 flex flex-col">
		<div class="flex p-3 justify-between">
			<h3 class="p-0 m-0"><span><?php _e("How to Use", "ml-slider"); ?></span></h3>

			<div class="m-0 flex">
				<button @click.prevent="useTitle = !useTitle" class="flex items-end">
					<i v-if="useTitle" class="text-blue-light mr-1 flex">
						<font-awesome-icon transform="grow-2" icon="toggle-on" />
					</i>
					<i v-else class="text-gray mr-1 rtl:mr-0 rtl:ml-1 flex">
						<font-awesome-icon transform="grow-2" icon="toggle-off" />
					</i>
					<?php _e("Toggle title", "ml-slider"); ?>
				</button>
			</div>
		</div>

		<div class="m-3 mt-2">
			<p class="mt-0"><?php _e('To display your slideshow, add the following shortcode (in orange) to your page. If adding the slideshow to your theme files, additionally include the surrounding PHP code (in gray).&lrm;', 'ml-slider'); ?></p>

			<pre id="shortcode" ref="shortcode" dir="ltr" class="text-gray text-sm">&lt;?php echo do_shortcode('<br>&emsp;&emsp;<div @click.prevent="copyShortcode($event)" class="text-orange cursor-pointer whitespace-normal inline">[metaslider <template v-if="useTitle">title="{{ current.title }}"</template><template v-else>id="{{ current.id }}"</template>]</div><br>'); ?&gt;</pre>

			<div class="flex mt-4 justify-between">
				<p class="m-0"><?php _e('Click shortcode to copy', 'ml-slider'); ?></p>
				<button @click.prevent="copyAll()" class="text-xs" title="<?php _e("Copy all code", "ml-slider"); ?>">
					<i class="text-gray mr-1 rtl:mr-0 rtl:ml-1">
						<font-awesome-icon transform="grow-2" icon="copy" />
					</i><?php _e("Copy all", "ml-slider"); ?>
				</button>
			</div>

		</div>
	</div>
</metaslider-shortcode>