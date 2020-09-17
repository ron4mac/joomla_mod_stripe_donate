<?php // no direct access
defined('_JEXEC') or die('Restricted access');

\JHtml::_('bootstrap.framework');
$document->addScript('https://js.stripe.com/v3/');
$document->addScript('modules/'.$moduleName.'/assets/'.$moduleName.'.js');
$modid = $module->id;
$ptoken = modStripedHelper::getToken($params);
?>
<div class="mod-striped">
	<div class="row-fluid">
		<div class="span6">
			<div><?php echo $params['contact-blurb']; ?></div>
			<form id="dcdf-<?php echo $modid; ?>">
				<fieldset>
					<input class="span12" name="name" type="text" placeholder="full name">
					<input class="span12" name="line1" type="text" placeholder="address1">
					<input class="span12" name="line2" type="text" placeholder="address2">
					<div class="controls controls-row">
						<input class="span7" name="city" type="text" placeholder="city">
						<input class="span2" name="state" type="text" placeholder="state">
						<input class="span3" name="postal_code" type="text" placeholder="zipcode">
					</div>
					<input class="span12" name="phone" type="text" placeholder="phone: (###) ###-####">
				</fieldset>
			</form>
		</div>
		<div class="span6 stripe-donate">
			<button class="btn setamnt" data-modid="<?php echo $modid; ?>" value="25">$25</button>
			<button class="btn setamnt" data-modid="<?php echo $modid; ?>" value="50">$50</button>
			<button class="btn setamnt" data-modid="<?php echo $modid; ?>" value="75">$75</button>
			<button class="btn setamnt" data-modid="<?php echo $modid; ?>" value="100">$100</button>
			<p class="sr-legal-text" data-i18n="sr-legal-text"></p>
			<div class="stripe-pay">
				<div class="input-prepend input-append">
					<span class="add-on">$</span>
					<input type="text" id="amount-input<?php echo $modid; ?>" class="text-right" />
					<span class="add-on">.00</span>
				</div>
				<p class="muted"><?php echo $params['submit-blurb']; ?></p>
				<button id="submit<?php echo $modid; ?>" class="btn btn-primary doit"
					data-modid="<?php echo $modid; ?>" data-loading-text="<i class='icon-lock icon-white'></i>"
					> DONATE
				</button>
				<div class="text-error" id="errmsg<?php echo $modid; ?>"></div>
			</div>
		</div>
	</div>
	<script>
		try {
			var stripe = Stripe("<?php echo $ptoken; ?>");
		} catch (err) {
			console.log(err);
			mod_striped_msg(err.message, <?php echo $modid; ?>)
		}
		document.querySelector("#submit<?php echo $modid; ?>").addEventListener("click", function (evt) {
			if (parseInt(document.getElementById("amount-input<?php echo $modid; ?>").value)) {
				jQuery(this).button("loading");
				createCheckoutSession(this.dataset.modid).then(function (data) {
					console.log(data);
					if (data.success == false) {
						mod_striped_msg(data.message, <?php echo $modid; ?>);
						return;
					}
					stripe.redirectToCheckout({
						sessionId: data.sessionId,
					}).then(handleResult);
				});
			} else {
				//alert("Unacceptable donation amount");
				mod_striped_msg("<?php echo JText::_('MOD_STRIPED_BAD_AMOUNT'); ?>", <?php echo $modid; ?>)
			}
		});

		/* Attach method */
		Array.from(document.getElementsByClassName("setamnt")).forEach(
			(element) => {
				element.addEventListener("click", updateAmount);
			}
		);
	</script>
</div>
