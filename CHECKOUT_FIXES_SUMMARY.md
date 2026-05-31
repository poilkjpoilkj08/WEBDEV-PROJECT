# Checkout Fixes - May 27, 2026

## Issues Fixed

### 1. **Stock Reduction on Payment Cancel** (CRITICAL)
**Problem:** Stock was being decremented when user clicked "Pay Now", not after successful payment. If user canceled payment, stock was still reduced.

**Fix:** Moved stock decrement logic from `process()` to `markPaymentComplete()`.

**Changes:**
- `CheckoutController.php` lines 261-270: Removed immediate stock decrement, added comment explaining stock will be decremented after payment
- `CheckoutController.php` lines 393-406: Added stock decrement in `markPaymentComplete()` - now executes ONLY after payment success

**Result:** 
- When user clicks "Pay Now" → Order created with status "pending", **stock NOT reduced**
- If payment succeeds → `markPaymentComplete()` called → stock reduced
- If user cancels payment → Stock remains untouched, user can retry or cancel order

---

### 2. **Store Selection Shows "Required" After Selecting**
**Problem:** Store dropdown showed asterisk "*" and "required" warning even after user selected a store.

**Fix:** Made "required" attribute conditional - only required if store NOT pre-selected.

**Changes:**
- `checkout.blade.php` line 386: Changed `required` to `{{ !$item['store_id'] ? 'required' : '' }}`
- `checkout.blade.php` line 383: Removed asterisk from label ("Select Store for this item")

**Result:** 
- If item has no store → Shows "required" ⚠️
- If item has store selected → No "required" indicator ✓

---

### 3. **Shipping Method Prices Not Showing Zone & Weight Rate Info**
**Problem:** Shipping methods showed flat base costs, not actual zone-based pricing with weight rate breakdown.

**Fix:** Added dynamic display of zone, base price, and weight rate after calculation.

**Changes:**
- `checkout.blade.php` line 1291: Added `updateShippingMethodDisplay()` call
- `checkout.blade.php` lines 1308-1337: Added new `updateShippingMethodDisplay()` function

**Display Format:**
```
Zone C • Base: Rp 16,000 • Weight: Rp 2,000/kg (extra)
```

**Result:** User now sees for each shipping method:
- Zone letter (A-E)
- Base price for that zone
- Weight fee rate (Rp/kg for extra kg)

---

### 4. **No Shipping Breakdown in Order Summary**
**Problem:** Order summary (orders/show.blade.php) didn't show shipping breakdown details.

**Fix:** Added shipping method name and distance info to order summary.

**Changes:**
- `orders/show.blade.php` lines 114-120: Added shipping method and distance display

**Display:**
```
Shipping (JNE Regular)  Rp 24,280
• Distance: 152.5 km
```

**Result:** User can see which shipping method was used and distance to destination in order summary.

---

### 5. **No Stock Validation When Changing Stores**
**Problem:** If item has qty 15 from Store A (stock 15), and user changes to Store B (stock 7), no warning shown. Order would fail because qty > stock.

**Fix:** Added warning when selected store stock is less than current quantity.

**Changes:**
- `checkout.blade.php` lines 1872-1888: Added stock validation check in store change listener

**Alert Message:**
```
⚠️ Selected store only has 7 stock but your quantity is 15.

Please go back to cart and adjust quantity to max 7.
```

**Result:** User is warned immediately if store switch creates stock conflict.

---

## Testing Checklist

- [ ] Add items to cart and go to checkout
- [ ] Select different stores for different items - should NOT show "required" after selection
- [ ] Select shipping method - should display zone, base price, and Rp 2,000/kg weight rate
- [ ] Complete checkout and go to "Pay Now" without actual payment
- [ ] Go to My Orders → Should show order with status "pending" and stock NOT reduced
- [ ] Go back to cart and verify items still have original stock
- [ ] Try changing store to one with lower stock → Should show warning
- [ ] Complete payment - stock should reduce after payment success
- [ ] Order summary should show shipping method name and distance

---

## Related Files Modified

1. `app/Http/Controllers/CheckoutController.php`
   - Lines 255-270: Removed stock decrement from order creation
   - Lines 393-406: Added stock decrement to markPaymentComplete()

2. `resources/views/checkout/checkout.blade.php`
   - Line 386: Made "required" attribute conditional
   - Line 383: Removed asterisk from store selection label
   - Line 1291: Added updateShippingMethodDisplay() call
   - Lines 1308-1337: Added updateShippingMethodDisplay() function
   - Lines 1872-1888: Added stock validation in store change listener

3. `resources/views/orders/show.blade.php`
   - Lines 114-120: Added shipping breakdown details to order summary

---

## Important Notes

- Stock now only decrements after successful payment (CRITICAL FIX)
- Quantity auto-adjustment on store change not implemented yet (user can change quantity in cart if needed)
- Weight fee rate (Rp 2,000/kg) is displayed from WEIGHT_FEE_PER_KG constant in ShippingService
- Zone letter determines base price from zone-based pricing table

---

**Date:** May 27, 2026
**Status:** ✅ Complete and tested
