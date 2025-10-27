<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\DigitalKey;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Platform;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use App\Support\Money;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function fake;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            [$admin, $customers] = $this->seedUsers();

            $categories = $this->seedCategories();
            $platforms = $this->seedPlatforms();

            [$digitalProducts, $physicalProducts] = $this->seedProducts($categories, $platforms);
            $products = $digitalProducts->concat($physicalProducts);

            $coupons = $this->seedCoupons();

            $this->seedReviews($customers, $products);
            $this->seedWishlists($customers, $products);
            $this->seedOrders($customers, $products, $coupons);
        });
    }

    private function seedUsers(): array
    {
        $admin = User::factory()->create([
            'name' => 'GameStore Admin',
            'email' => 'admin@gamestore.test',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $customers = User::factory()
            ->count(15)
            ->create();

        return [$admin, $customers];
    }

    private function seedCategories(): Collection
    {
        $rootNames = [
            'Action',
            'Adventure',
            'Role Playing',
            'Strategy',
            'Sports',
            'Racing',
        ];

        $roots = collect($rootNames)->map(function (string $name) {
            return Category::query()->create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        });

        $childrenMap = [
            'Action' => ['Shooter', 'Platformer'],
            'Role Playing' => ['Action RPG', 'JRPG'],
            'Strategy' => ['Real Time Strategy'],
            'Sports' => ['Arcade Sports'],
        ];

        $children = collect();

        foreach ($childrenMap as $parentName => $childNames) {
            $parent = $roots->firstWhere('name', $parentName);

            if (!$parent) {
                continue;
            }

            foreach ($childNames as $name) {
                $children->push(Category::query()->create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'parent_id' => $parent->id,
                ]));
            }
        }

        return $roots->merge($children);
    }

    private function seedPlatforms(): Collection
    {
        $platformNames = [
            'PlayStation 5',
            'Xbox Series X',
            'Nintendo Switch',
            'PC',
            'Steam',
            'Epic Games Store',
        ];

        return collect($platformNames)->map(function (string $name) {
            return Platform::query()->create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        });
    }

    /**
     * @return array{0: EloquentCollection<int, Product>, 1: EloquentCollection<int, Product>}
     */
    private function seedProducts(Collection $categories, Collection $platforms): array
    {
        $categoryIds = $categories->pluck('id');

        $digitalProducts = Product::factory()
            ->count(20)
            ->digital()
            ->state(fn () => ['category_id' => $categoryIds->random()])
            ->create();

        $physicalProducts = Product::factory()
            ->count(20)
            ->physical()
            ->state(fn () => ['category_id' => $categoryIds->random()])
            ->create();

        $all = $digitalProducts->concat($physicalProducts);

        $all->each(function (Product $product) use ($platforms): void {
            $platformIds = $platforms
                ->shuffle()
                ->take(rand(1, min(3, $platforms->count())))
                ->pluck('id')
                ->all();

            $product->platforms()->sync($platformIds);

            ProductImage::factory()
                ->count(rand(3, 5))
                ->for($product)
                ->sequence(fn (Sequence $sequence) => ['sort_order' => $sequence->index + 1])
                ->create();

            if (!$product->is_digital) {
                $product->inventory()->create([
                    'quantity' => rand(80, 200),
                ]);
            }
        });

        $digitalProducts->each(function (Product $product): void {
            DigitalKey::factory()
                ->count(2)
                ->create([
                    'product_id' => $product->id,
                ]);
        });

        return [$digitalProducts, $physicalProducts];
    }

    private function seedCoupons(): Collection
    {
        return collect([
            Coupon::query()->create([
                'code' => 'GAMER10',
                'type' => CouponType::Percent,
                'value' => 10,
                'starts_at' => now()->subMonth(),
                'ends_at' => null,
                'usage_limit' => 500,
                'used_count' => 0,
                'active' => true,
            ]),
            Coupon::query()->create([
                'code' => 'LEVELUP5',
                'type' => CouponType::Fixed,
                'value' => 5,
                'starts_at' => now()->subWeeks(2),
                'ends_at' => now()->addMonths(6),
                'usage_limit' => null,
                'used_count' => 0,
                'active' => true,
            ]),
            Coupon::query()->create([
                'code' => 'SUMMER20',
                'type' => CouponType::Percent,
                'value' => 20,
                'starts_at' => now()->subWeeks(1),
                'ends_at' => now()->addWeeks(3),
                'usage_limit' => 250,
                'used_count' => 0,
                'active' => true,
            ]),
        ]);
    }

    private function seedReviews(EloquentCollection $customers, EloquentCollection $products): void
    {
        $target = 100;
        $count = 0;

        $products->shuffle()->each(function (Product $product) use ($customers, $target, &$count) {
            if ($count >= $target) {
                return false;
            }

            $reviewers = $customers->shuffle()->take(rand(3, 5));

            foreach ($reviewers as $user) {
                if ($count >= $target) {
                    break;
                }

                Review::query()->create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'rating' => rand(3, 5),
                    'comment' => fake()->paragraph(),
                    'approved' => rand(0, 100) > 30,
                ]);

                $count++;
            }

            return null;
        });
    }

    private function seedWishlists(EloquentCollection $customers, EloquentCollection $products): void
    {
        $customers->shuffle()->take(10)->each(function (User $user) use ($products): void {
            $productIds = $products->shuffle()->take(rand(5, 8))->pluck('id');

            foreach ($productIds as $productId) {
                Wishlist::query()->firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
            }
        });
    }

    private function seedOrders(EloquentCollection $customers, EloquentCollection $products, Collection $coupons): void
    {
        $vatRate = (float) env('VAT_RATE', 0.19);
        $statuses = [
            OrderStatus::Paid,
            OrderStatus::Paid,
            OrderStatus::Paid,
            OrderStatus::RequiresPayment,
            OrderStatus::Pending,
            OrderStatus::Failed,
            OrderStatus::Canceled,
        ];

        for ($i = 0; $i < 12; $i++) {
            $status = $statuses[$i % count($statuses)];
            $user = $customers->random();
            $billing = [
                'name' => $user->name,
                'doc' => fake()->bothify('#########'),
                'email' => $user->email,
                'phone' => fake()->e164PhoneNumber(),
            ];

            $coupon = null;
            if (in_array($status, [OrderStatus::Paid, OrderStatus::RequiresPayment], true) && $coupons->isNotEmpty() && rand(0, 100) > 60) {
                $coupon = $coupons->random();
            }

            $order = Order::query()->create([
                'user_id' => $user->id,
                'subtotal_cents' => 0,
                'discount_cents' => 0,
                'tax_cents' => 0,
                'total_cents' => 0,
                'currency' => 'USD',
                'status' => OrderStatus::Pending,
                'coupon_code' => $coupon?->code,
                'billing_data' => $billing,
                'shipping_data' => null,
            ]);

            $orderProducts = $products->shuffle()->take(rand(2, 4));

            $subtotal = 0;
            $requiresShipping = false;

            foreach ($orderProducts as $product) {
                $quantity = $product->is_digital ? 1 : rand(1, 3);

                $orderItem = $order->items()->create([
                    'product_id' => $product->id,
                    'title_snapshot' => $product->title,
                    'unit_price_cents' => $product->price_cents,
                    'quantity' => $quantity,
                    'is_digital' => $product->is_digital,
                ]);

                $subtotal += $product->price_cents * $quantity;

                if ($product->is_digital) {
                    $key = $product->digitalKeys()->available()->first();

                    if ($key) {
                        $key->assignTo($orderItem);
                    }
                } else {
                    $requiresShipping = true;

                    if ($product->inventory && $product->inventory->quantity >= $quantity) {
                        $product->inventory->decrease($quantity);
                    }
                }
            }

            $subtotalMoney = new Money($subtotal, 'USD');
            $discountMoney = $coupon ? $coupon->applyTo($subtotalMoney) : new Money(0, 'USD');
            $taxable = max($subtotalMoney->amount - $discountMoney->amount, 0);
            $normalizedRate = $vatRate >= 1 ? $vatRate / 100 : $vatRate;
            $taxMoney = new Money((int) round($taxable * $normalizedRate), 'USD');
            $totalMoney = new Money(max($subtotalMoney->amount - $discountMoney->amount + $taxMoney->amount, 0), 'USD');

            $order->update([
                'subtotal_cents' => $subtotalMoney->amount,
                'discount_cents' => $discountMoney->amount,
                'tax_cents' => $taxMoney->amount,
                'total_cents' => $totalMoney->amount,
                'shipping_data' => $requiresShipping ? [
                    'address' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'country' => fake()->country(),
                    'zip' => fake()->postcode(),
                ] : null,
            ]);

            switch ($status) {
                case OrderStatus::Paid:
                    $order->markAsPaid();

                    if ($coupon) {
                        $coupon->registerUsage();
                    }

                    break;
                case OrderStatus::Failed:
                    $order->markAsFailed();
                    break;
                case OrderStatus::Canceled:
                    $order->markAsCanceled();
                    break;
                default:
                    $order->update(['status' => $status]);
                    break;
            }

            $paymentStatus = match ($status) {
                OrderStatus::Paid => PaymentStatus::Approved,
                OrderStatus::Failed => PaymentStatus::Declined,
                OrderStatus::Canceled => PaymentStatus::Voided,
                default => PaymentStatus::Pending,
            };

            Payment::query()->create([
                'order_id' => $order->id,
                'provider' => 'WOMPI',
                'provider_reference' => 'SEED-'.$order->number,
                'status' => $paymentStatus,
                'amount_cents' => $totalMoney->amount,
                'raw_payload' => ['seed' => true],
                'received_at' => $paymentStatus->isTerminal() ? now() : null,
            ]);
        }
    }
}
