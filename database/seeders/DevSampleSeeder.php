<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * SAMPLE catalog data for local development ONLY.
 *
 * - Every name and text is clearly labelled «نمونه» (sample). None of it is a real claim.
 * - Prices here are made up for testing the pages. Real prices are entered by the admin.
 * - No images: the shop shows the brand placeholder until real photos exist.
 * - Safe to run more than once (updates the same rows by slug).
 *
 * Covers the states the shop pages must handle:
 *   two weights · out of stock · no price yet · hidden (inactive) product
 *
 *   php artisan db:seed --class=DevSampleSeeder
 */
class DevSampleSeeder extends Seeder
{
    private const SAMPLE_NOTE = 'این متن نمونه است و فقط برای آزمایش ظاهر سایت در محیط توسعه نوشته شده.';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('DevSampleSeeder only runs in local/testing environments.');
        }

        $honey = Category::updateOrCreate(['slug' => 'نمونه-عسل'], ['name' => 'عسل (نمونه)', 'sort' => 1]);
        $other = Category::updateOrCreate(['slug' => 'نمونه-سایر'], ['name' => 'سایر محصولات (نمونه)', 'sort' => 2]);

        // 1) Normal product with two weights.
        $this->product($honey, 'نمونه-عسل-عشایری', 'عسل عشایری (نمونه)', active: true, variants: [
            ['title' => '۱ کیلوگرم', 'weight_grams' => 1000, 'price' => 480000, 'stock' => 20],
            ['title' => '۲ کیلوگرم', 'weight_grams' => 2000, 'price' => 930000, 'stock' => 8],
        ]);

        // 2) One weight, in stock.
        $this->product($honey, 'نمونه-عسل-کوهستان', 'عسل کوهستان (نمونه)', active: true, variants: [
            ['title' => '۱ کیلوگرم', 'weight_grams' => 1000, 'price' => 520000, 'stock' => 5],
        ]);

        // 3) Out of stock.
        $this->product($honey, 'نمونه-عسل-ناموجود', 'عسل ناموجود (نمونه)', active: true, variants: [
            ['title' => '۱ کیلوگرم', 'weight_grams' => 1000, 'price' => 450000, 'stock' => 0],
        ]);

        // 4) Price not set yet (cannot be bought).
        $this->product($other, 'نمونه-بدون-قیمت', 'محصول بدون قیمت (نمونه)', active: true, variants: [
            ['title' => '۱ کیلوگرم', 'weight_grams' => 1000, 'price' => null, 'stock' => 10],
        ]);

        // 5) Hidden product (must NOT appear in the shop).
        $this->product($honey, 'نمونه-مخفی', 'محصول مخفی (نمونه)', active: false, variants: [
            ['title' => '۱ کیلوگرم', 'weight_grams' => 1000, 'price' => 400000, 'stock' => 10],
        ]);
    }

    /**
     * @param  array<int, array{title: string, weight_grams: int, price: ?int, stock: int}>  $variants
     */
    private function product(Category $category, string $slug, string $name, bool $active, array $variants): void
    {
        $product = Product::updateOrCreate(['slug' => $slug], [
            'category_id' => $category->id,
            'name' => $name,
            'short_description' => 'توضیح کوتاه نمونه برای کارت محصول.',
            'description' => self::SAMPLE_NOTE."\n\nاینجا توضیحات کامل محصول قرار می‌گیرد: طعم، رنگ، شرایط نگهداری و نکته‌های مصرف.",
            'story' => self::SAMPLE_NOTE."\n\nاینجا داستان واقعی محصول نوشته می‌شود: منطقه، ایل و روش برداشت.",
            'storage_instructions' => 'متن نمونه: اینجا شرایط نگهداری محصول نوشته می‌شود.',
            'is_active' => $active,
        ]);

        $product->variants()->delete();

        foreach (array_values($variants) as $i => $variant) {
            $product->variants()->create($variant + ['sort' => $i, 'is_active' => true]);
        }
    }
}
