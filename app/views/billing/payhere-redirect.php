<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= Helpers::e($title) ?></title>
<style>
  body { font-family: sans-serif; background: #f9fafb; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
  .box { text-align: center; color: #374151; }
  .spinner { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
  @keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div class="box">
  <div class="spinner"></div>
  <p>Redirecting to PayHere…</p>
</div>

<form id="payhere-form" method="POST" action="<?= Helpers::e($checkoutUrl) ?>">
  <input type="hidden" name="merchant_id" value="<?= Helpers::e($merchantId) ?>">
  <input type="hidden" name="return_url" value="<?= Helpers::e($returnUrl) ?>">
  <input type="hidden" name="cancel_url" value="<?= Helpers::e($cancelUrl) ?>">
  <input type="hidden" name="notify_url" value="<?= Helpers::e($notifyUrl) ?>">
  <input type="hidden" name="order_id" value="<?= Helpers::e($orderId) ?>">
  <input type="hidden" name="items" value="<?= Helpers::e($itemsName) ?>">
  <input type="hidden" name="currency" value="<?= Helpers::e($currency) ?>">
  <input type="hidden" name="amount" value="<?= Helpers::e($amount) ?>">
  <input type="hidden" name="first_name" value="<?= Helpers::e($firstName) ?>">
  <input type="hidden" name="last_name" value="<?= Helpers::e($lastName) ?>">
  <input type="hidden" name="email" value="<?= Helpers::e($email) ?>">
  <input type="hidden" name="phone" value="">
  <input type="hidden" name="address" value="">
  <input type="hidden" name="city" value="">
  <input type="hidden" name="country" value="">
  <input type="hidden" name="hash" value="<?= Helpers::e($hash) ?>">
  <noscript><button type="submit">Continue to PayHere</button></noscript>
</form>

<script>document.getElementById('payhere-form').submit();</script>
</body>
</html>
