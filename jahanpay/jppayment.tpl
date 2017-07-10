
<!-- JahanPay Payment Module -->
<p class="payment_module">
    <a href="javascript:$('#jahanpay').submit();" title="{l s='Online payment with jahanpay' mod='jahanpay'}">
        <img src="modules/jahanpay/jahanpay.png" alt="{l s='Online payment with jahanpay' mod='jahanpay'}" />
		{l s=' پرداخت با کارتهای اعتباری / نقدی بانک های عضو شتاب توسط دروازه پرداخت جهان پی ' mod='jahanpay'}
<br>
</a></p>

<form action="modules/jahanpay/jp.php?do=payment" method="post" id="jahanpay" class="hidden">
    <input type="hidden" name="orderId" value="{$orderId}" />
</form>
<br><br>
<!-- End of JahanPay Payment Module-->
