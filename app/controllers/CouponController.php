<?php
/**
 * CouponController – public AJAX endpoint for coupon validation
 */
class CouponController extends BaseController
{
    /**
     * POST /coupon/validate
     * Body: code, plan_slug, email, price (optional — for discount preview)
     * Returns JSON: {valid, discount_type, discount_value, discount_amount, message}
     */
    public function validate(): void
    {
        $code     = Helpers::sanitize($_POST['code'] ?? '');
        $planSlug = Helpers::sanitize($_POST['plan_slug'] ?? '');
        $email    = Helpers::sanitize($_POST['email'] ?? '');
        $price    = max(0, (float)($_POST['price'] ?? 0));

        if (!$code) {
            $this->json(['valid' => false, 'message' => 'Please enter a coupon code.']);
        }

        [$coupon, $reason] = Coupon::check($code, $email, $planSlug);

        if (!$coupon) {
            $this->json(['valid' => false, 'message' => $reason]);
        }

        $discountAmount = $price > 0 ? Coupon::discountAmount($coupon, $price) : null;
        $final          = $discountAmount !== null ? max(0, $price - $discountAmount) : null;

        $this->json([
            'valid'           => true,
            'code'            => $coupon['code'],
            'discount_type'   => $coupon['discount_type'],
            'discount_value'  => (float)$coupon['discount_value'],
            'discount_amount' => $discountAmount,
            'final_price'     => $final,
            'message'         => $coupon['discount_type'] === 'percentage'
                ? (int)$coupon['discount_value'] . '% discount applied!'
                : '$' . number_format((float)$coupon['discount_value'], 2) . ' off applied!',
        ]);
    }
}
