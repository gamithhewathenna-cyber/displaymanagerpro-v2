<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= Helpers::e($title) ?></title>
<style>
  body { font-family: sans-serif; background: #f9fafb; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 16px; }
  .box { text-align: center; color: #374151; background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px 28px; max-width: 380px; width: 100%; }
  .plan-name { font-size: 13px; color: #9ca3af; margin-bottom: 4px; }
  .lkr-amount { font-size: 32px; font-weight: 800; color: #111827; margin-bottom: 4px; }
  .usd-amount { font-size: 13px; color: #9ca3af; margin-bottom: 20px; }
  .rate-note { font-size: 11px; color: #9ca3af; margin-top: 16px; line-height: 1.5; }
  .btn { display: block; width: 100%; background: #ec4899; color: #fff; font-weight: 600; padding: 13px; border: none; border-radius: 10px; font-size: 14px; cursor: pointer; text-decoration: none; }
  .btn:hover { background: #db2777; }
  .cancel-link { display: block; margin-top: 12px; font-size: 13px; color: #9ca3af; text-decoration: none; }
  .cancel-link:hover { color: #6b7280; }
</style>
</head>
<body>
<div class="box">
  <div class="plan-name"><?= Helpers::e($itemsName) ?></div>
  <div class="lkr-amount">Rs. <?= Helpers::e($amount) ?></div>
  <div class="usd-amount">≈ $<?= Helpers::e($usdAmount) ?> USD</div>

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
    <button type="submit" class="btn">Continue to PayHere</button>
  </form>
  <a href="/billing" class="cancel-link">Cancel and go back</a>

  <p class="rate-note">You'll be charged in Sri Lankan Rupees (LKR) via PayHere.<br>Rate used: 1 USD = <?= number_format($exchangeRate, 2) ?> LKR.</p>
</div>
</body>
</html>
