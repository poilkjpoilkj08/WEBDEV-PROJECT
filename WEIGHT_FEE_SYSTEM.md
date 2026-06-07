# Weight Fee System Update (May 27, 2026)

## Summary
Updated shipping weight fee calculation from **Rp 3,000/kg** to **Rp 2,000/kg** for all couriers.

## New Pricing Formula
```
Total Shipping Cost = Zone Base + Weight Fee + Service Fee
```

### Weight Fee Calculation
| Berat           | Biaya           |
| --------------- | --------------- |
| 1kg pertama     | Included in zone_base |
| +1kg berikutnya | +Rp 2,000 per kg |

**Example: 2.64kg package**
- 1kg included (in zone_base)
- 1.64kg extra × Rp 2,000 = **Rp 3,280 weight fee**

## Complete Example: 2.64kg JNE REG to Zone C
```
Zone Base:        Rp 16,000 (for Zone C, REG level)
Weight Fee:       Rp 3,280  (1.64kg extra × Rp 2,000)
Service Fee:      Rp 5,000  (REG surcharge)
─────────────────────────────────
Total:            Rp 24,280
```

## All Couriers (2.64kg Zone C)
- **JNE REG**: Rp 24,280 (16k + 3.28k + 5k)
- **J&T REG**: Rp 22,280 (14k + 3.28k + 5k)
- **SiCepat REG**: Rp 24,280 (16k + 3.28k + 5k)
- **Pos Biasa**: Rp 10,780 (7.5k + 3.28k + 0)
- **GoSend Instant**: Rp 66,280 (33k + 3.28k + 30k)
- **Grab Instant**: Rp 70,280 (37k + 3.28k + 30k)

## Code Changes

### 1. ShippingService.php
**Updated:** `WEIGHT_FEE_PER_KG` constant (new, replaces `EXTRA_KG_COST`)
```php
private const WEIGHT_FEE_PER_KG = [
    'jne' => 2000,      // +2k per extra kg
    'jnt' => 2000,      // +2k per extra kg
    'sicepat' => 2000,  // +2k per extra kg
    'pos' => 2000,      // +2k per extra kg
    'gosend' => 2000,   // +2k per extra kg
    'grab' => 2000,     // +2k per extra kg
];
```

**Updated:** `calculateShippingCost()` method
```php
// OLD (before):
$weight_fee = round($extra_kg * self::EXTRA_KG_COST); // 3000 per kg

// NEW (after):
$weight_fee_rate = self::WEIGHT_FEE_PER_KG[$courier_key] ?? 2000;
$weight_fee = round($extra_kg * $weight_fee_rate); // 2000 per kg (per courier)
```

### 2. checkout.blade.php (No changes needed)
Already correctly displays:
- Base: zone_base
- Weight Fee: weight_fee
- Service: service_surcharge

Cart items already have `data-weight-grams` from book.weight_grams.

## Testing Instructions

### 1. Add items to cart
- Add 3+ items from any store
- Total weight should be > 1kg (default: 300g per book)
- Example: 3 items × 350g = 1,050g (1.05kg)

### 2. Go to checkout and calculate shipping

### 3. Check breakdown display
Should show:
```
Store #1 - Zone C (Same Island, Different Province) - Rp XXX,XXX
1.05kg + 0.05kg extra

• Base:          Rp 16,000
• Weight Fee:    Rp 100      ← (0.05kg × Rp 2,000)
• Service (reg): Rp 5,000
```

### 4. Browser Console (F12)
Look for logs with `[CALC]` prefix:
```
[CALC] Book #1: 350g × 1 = 350g
[CALC] Book #2: 350g × 1 = 350g
[CALC] Book #3: 350g × 1 = 350g
[CALC] Store #1: Total Weight = 1050g = 1.050kg

[CALC] API Response Store #1: {
  cost: 21100,
  breakdown: {
    weight_fee: 100,  ← Verify this shows correct amount
    ...
  }
}
```

## Courier-Specific Pricing (Future)
To set different weight fees per courier, simply update `WEIGHT_FEE_PER_KG`:
```php
private const WEIGHT_FEE_PER_KG = [
    'jne' => 2000,      // Rp 2k per extra kg
    'jnt' => 1500,      // Could be Rp 1.5k per extra kg (cheaper)
    'sicepat' => 2000,
    'pos' => 2000,
    'gosend' => 2500,   // Could be Rp 2.5k per extra kg (premium)
    'grab' => 2500,
];
```

## Verification
Backend test via Tinker:
```php
$shipping = app(\App\Services\ShippingService::class);
$result = $shipping->calculateShippingCost(2640, 'C', 'jne_reg');
// Returns: weight_fee = 3280 (1.64kg × 2000) ✓
```

---
**Updated:** May 27, 2026
**Files Modified:** 
- `/app/Services/ShippingService.php` (constant + calculation logic)
- `/resources/views/checkout/checkout.blade.php` (added detailed logging - no UI changes)
