{l s='Your order on %s is complete.' sprintf=$shop_name mod='jahanpay'}
		{if !isset($reference)}
			<br /><br />{l s='Your order number' mod='jahanpay'}: {$id_order}
		{else}
			<br /><br />{l s='Your order number' mod='jahanpay'}: {$id_order}
			<br /><br />{l s='Your order reference' mod='jahanpay'}: {$reference}
		{/if}		<br /><br />{l s='An email has been sent with this information.' mod='jahanpay'}
		<br /><br /> <strong>{l s='Your order will be sent as soon as posible.' mod='jahanpay'}</strong>
		<br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='jahanpay'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='jahanpay'}</a>.
	</p><br />