<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

modStripedHelper::attachCCD($params, $input->get('session_id', '', 'string'));

?>
<div class="mod-striped">
	<section class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<?php echo $params['thank-you']; ?>
			</div>
		</div>
	</section>
</div>