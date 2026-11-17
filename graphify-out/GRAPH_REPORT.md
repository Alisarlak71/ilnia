# Graph Report - ilnia-shop  (2026-11-17)

## Corpus Check
- 239 files · ~49,015 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 24 file(s) not represented in the graph (top: (none) 17, .woff2 3, .css 2)

## Summary
- 1522 nodes · 3233 edges · 152 communities (49 shown, 103 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 190 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6d180567`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- ProductVariant
- OtpLoginTest
- CancelUnpaidOrdersCommand.php
- OrderStatus
- TestCase
- OrderForm.php
- Filament Resources
- Address Book Tests
- Illuminate\Database\Eloquent\Factories\HasFactory
- CatalogAdminTest.php
- web.php
- Admin Tables
- Order Relation Managers
- Payment Service Tests
- DevSampleSeederTest.php
- Frontend Build Tooling
- Orders Admin Tests
- Digits
- TestCase
- Illuminate\Database\Eloquent\Factories\Factory
- SMS Service & Kavenegar
- Admin Panel Access
- PaymentServiceTest.php
- ShippingMethod
- Product Images
- Catalog Model Tests
- CatalogAdminTest
- Input Normalizers (Mobile, Weight)
- Product Page Tests
- Admin List Pages
- SupportButtonTest
- App Boot & Security
- AGENTS.md (Agent Guide)
- Filament\Tables\Table
- SmsException
- Product
- ErrorPagesTest.php
- 0001_01_01_000000_create_users_table
- ProductVariant
- Page
- CartPageTest
- StoreStats
- AccountOrdersTest
- Category
- PaymentController.php
- PHPUnit\Framework\TestCase
- FreeShippingMessageTest
- HasPersianSlug
- Filament\Resources\Pages\EditRecord
- SeoController
- IntegerInput.php
- composer.json
- composer.json (2)
- OrdersAdminTest.php
- ProductListPageTest
- app
- composer.json (3)
- AdminPanelAccessTest
- Filament\Schemas\Schema
- SendOrderConfirmationSms
- composer.json (4)
- Illuminate\Database\Eloquent\Builder
- composer.json (5)
- ProductVariantFactory
- addresses-page
- composer.json (6)
- 0001_01_01_000001_create_cache_table
- 2026_11_11_231149_add_is_admin_to_users_table
- 2026_11_11_232034_create_categories_table
- 2026_11_11_232036_create_products_table
- 2026_11_11_234636_add_storage_instructions_to_products_table
- 2026_11_12_000958_add_mobile_to_users_table
- PagesTest
- product-purchase
- Order
- checkout-page
- 2026_11_11_232037_create_product_images_table
- 2026_11_11_232039_create_product_variants_table
- 2026_11_12_001000_create_otp_codes_table
- 2026_11_12_001553_create_addresses_table
- 2026_11_12_002802_create_shipping_methods_table
- 2026_11_12_002804_create_orders_table
- 2026_11_12_002806_create_order_items_table
- 2026_11_12_002807_create_payments_table
- 2026_11_12_100000_create_pages_table
- cart-page
- login-page
- composer.json (7)
- composer.json (8)
- OrderResource
- header.blade.php
- show
- 500
- 503
- PageResource
- PaymentService
- KavenegarSmsDriver
- FooterTest
- SeoTest
- ScreenSmsDriverTest
- bottom-nav.blade.php

## God Nodes (most connected - your core abstractions)
1. `TestCase` - 75 edges
2. `OrderStatus` - 66 edges
3. `Order` - 59 edges
4. `ProductVariant` - 55 edges
5. `CartService` - 54 edges
6. `User` - 48 edges
7. `Product` - 44 edges
8. `PaymentServiceTest` - 33 edges
9. `OtpLoginTest` - 32 edges
10. `ShippingMethod` - 31 edges

## Surprising Connections (you probably didn't know these)
- `OrderServiceTest` --references--> `Address`  [EXTRACTED]
  tests/Feature/OrderServiceTest.php → app/Models/Address.php
- `PaymentServiceTest` --references--> `Order`  [EXTRACTED]
  tests/Feature/PaymentServiceTest.php → app/Models/Order.php
- `CancelUnpaidOrdersTest` --references--> `ProductVariant`  [EXTRACTED]
  tests/Feature/CancelUnpaidOrdersTest.php → app/Models/ProductVariant.php
- `PaymentResultPageTest` --references--> `ProductVariant`  [EXTRACTED]
  tests/Feature/PaymentResultPageTest.php → app/Models/ProductVariant.php
- `PaymentServiceTest` --references--> `ProductVariant`  [EXTRACTED]
  tests/Feature/PaymentServiceTest.php → app/Models/ProductVariant.php

## Import Cycles
- None detected.

## Communities (152 total, 103 thin omitted)

### Community 0 - "ProductVariant"
Cohesion: 0.09
Nodes (5): CartException, CartPage, CartService, CartServiceTest, OrderServiceTest

### Community 1 - "OtpLoginTest"
Cohesion: 0.08
Nodes (16): LoginPage, OtpService, {closure#1}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#2}(), {closure#3}() (+8 more)

### Community 3 - "OrderStatus"
Cohesion: 0.06
Nodes (20): OrderStatus, Cancelled, Delivered, NeedsAttention, Paid, PaymentFailed, PendingPayment, Processing (+12 more)

### Community 4 - "TestCase"
Cohesion: 0.12
Nodes (4): EditPage, CartIcon, BottomNavTest, OrderPricingTest

### Community 5 - "OrderForm.php"
Cohesion: 0.13
Nodes (11): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#7}(), {closure#8}(), {closure#5}(), {closure#2}() (+3 more)

### Community 6 - "Filament Resources"
Cohesion: 0.22
Nodes (6): CategoryResource, CreateCategory, CreateProduct, ProductResource, CreateShippingMethod, ShippingMethodResource

### Community 8 - "Illuminate\Database\Eloquent\Factories\HasFactory"
Cohesion: 0.17
Nodes (3): Address, OrderItem, AddressFactory

### Community 9 - "CatalogAdminTest.php"
Cohesion: 0.21
Nodes (4): {closure#1}(), {closure#2}(), ProductImage, ProductImageFactory

### Community 10 - "web.php"
Cohesion: 0.16
Nodes (6): OrderController, Controller, HomeController, PageController, ProductController, POST /logout()

### Community 11 - "Admin Tables"
Cohesion: 0.22
Nodes (4): {closure#2}(), {closure#4}(), LowStock, OrdersToShip

### Community 12 - "Order Relation Managers"
Cohesion: 0.15
Nodes (9): {closure#1}(), {closure#2}(), {closure#3}(), {closure#5}(), {closure#6}(), {closure#2}(), {closure#3}(), Jalali (+1 more)

### Community 14 - "DevSampleSeederTest.php"
Cohesion: 0.12
Nodes (4): DatabaseSeeder, DevSampleSeeder, ShippingMethodSeeder, DevSampleSeederTest

### Community 15 - "Frontend Build Tooling"
Cohesion: 0.09
Nodes (23): dependencies, vazirmatn, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+15 more)

### Community 17 - "Digits"
Cohesion: 0.15
Nodes (7): {closure#10}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#9}(), Digits, DigitsTest

### Community 18 - "TestCase"
Cohesion: 0.19
Nodes (3): LayoutTest, LogoComponentTest, TestCase

### Community 19 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.12
Nodes (5): CategoryFactory, OrderItemFactory, PaymentFactory, ProductFactory, ShippingMethodFactory

### Community 22 - "PaymentServiceTest.php"
Cohesion: 0.11
Nodes (9): {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}(), {closure#3}(), {closure#5}(), {closure#6}(), {closure#3}() (+1 more)

### Community 23 - "ShippingMethod"
Cohesion: 0.05
Nodes (10): AddressesPage, CheckoutPage, AddressForm, ShippingMethod, OrderPricing, OrderService, OrderTotals, ShippingCalculator (+2 more)

### Community 29 - "Admin List Pages"
Cohesion: 0.19
Nodes (5): ListCategories, ListOrders, ListPages, ListProducts, ListShippingMethods

### Community 31 - "App Boot & Security"
Cohesion: 0.11
Nodes (3): AppServiceProvider, GET /__ip(), SecurityTest

### Community 32 - "AGENTS.md (Agent Guide)"
Cohesion: 0.11
Nodes (14): AGENTS.md (Agent Guide), Project .env Variables (KAVENEGAR_*, SMS_DRIVER, ZARINPAL_*), Fixed Tech Stack (PHP 8.5 + Laravel + Livewire + Filament + Tailwind), Brand Sheet Preview (preview.html), Brand Color Palette (ink, ink-soft, gold, gold-deep, honey, cream, paper, muted), Brand Assets README, brand.json (name, slogans, colors, paths), Logo Mark SVGs (logo-mark-gold / logo-mark-ink / favicon) (+6 more)

### Community 33 - "Filament\Tables\Table"
Cohesion: 0.11
Nodes (9): CategoriesTable, {closure#2}(), {closure#2}(), {closure#3}(), {closure#4}(), OrdersTable, ProductsTable, {closure#1}() (+1 more)

### Community 34 - "SmsException"
Cohesion: 0.16
Nodes (4): OrderException, OtpException, SmsException, Mobile

### Community 35 - "Product"
Cohesion: 0.18
Nodes (6): {closure#2}(), {closure#3}(), {closure#4}(), {closure#6}(), {closure#1}(), Product

### Community 36 - "ErrorPagesTest.php"
Cohesion: 0.14
Nodes (3): ErrorPagesTest, LivewireSetupTest, SetupCheckComponent

### Community 37 - "0001_01_01_000000_create_users_table"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 38 - "ProductVariant"
Cohesion: 0.27
Nodes (3): {closure#1}(), ProductVariant, CartLine

### Community 39 - "Page"
Cohesion: 0.29
Nodes (4): {closure#3}(), {closure#4}(), Page, PageSeeder

### Community 43 - "Category"
Cohesion: 0.18
Nodes (4): {closure#2}(), {closure#3}(), Category, HasPersianSlug

### Community 48 - "Filament\Resources\Pages\EditRecord"
Cohesion: 0.24
Nodes (3): EditCategory, EditProduct, EditShippingMethod

### Community 50 - "IntegerInput.php"
Cohesion: 0.24
Nodes (4): {closure#3}(), {closure#4}(), SecurityHeaders, {closure#1}()

### Community 51 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 52 - "composer.json (2)"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 53 - "OrdersAdminTest.php"
Cohesion: 0.22
Nodes (3): EditOrder, ItemsRelationManager, PaymentsRelationManager

### Community 56 - "app"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 57 - "composer.json (3)"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 59 - "Filament\Schemas\Schema"
Cohesion: 0.13
Nodes (4): IntegerInput, CategoryForm, ProductForm, ShippingMethodForm

### Community 62 - "composer.json (4)"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 64 - "composer.json (5)"
Cohesion: 0.33
Nodes (6): require, filament/filament, laravel/framework, laravel/tinker, livewire/livewire, php

### Community 69 - "addresses-page"
Cohesion: 0.40
Nodes (4): cancel, create, delete({{ $address->id }}), edit({{ $address->id }})

### Community 70 - "composer.json (6)"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 79 - "product-purchase"
Cohesion: 0.50
Nodes (3): addToCart, decrement, increment

### Community 80 - "Order"
Cohesion: 0.19
Nodes (5): {closure#11}(), {closure#1}(), {closure#4}(), {closure#1}(), Order

### Community 81 - "checkout-page"
Cohesion: 0.50
Nodes (3): cancelAddress, pay, startAddingAddress

### Community 91 - "cart-page"
Cohesion: 0.50
Nodes (3): decrement({{ $variant->id }}), increment({{ $variant->id }}), remove({{ $variant->id }})

### Community 94 - "composer.json (7)"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 95 - "composer.json (8)"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 150 - "PaymentService"
Cohesion: 0.19
Nodes (4): CancelUnpaidOrdersCommand, PaymentException, Payment, PaymentService

### Community 153 - "KavenegarSmsDriver"
Cohesion: 0.33
Nodes (3): {closure#1}(), KavenegarSmsDriver, LogSmsDriver

## Ambiguous Edges - Review These
- `Brand Sheet Preview (preview.html)` → `Self-hosted Fonts (not Google Fonts)`  [AMBIGUOUS]
  brand/preview.html · relation: conceptually_related_to

## Knowledge Gaps
- **40 isolated node(s):** `PendingPayment`, `Paid`, `Processing`, `Shipped`, `Delivered` (+35 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 499 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **103 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `TestCase` connect `TestCase` to `ProductVariant`, `OtpLoginTest`, `OrderStatus`, `TestCase`, `OrderForm.php`, `Address Book Tests`, `CatalogAdminTest.php`, `Admin Tables`, `Order Relation Managers`, `Payment Service Tests`, `DevSampleSeederTest.php`, `Orders Admin Tests`, `SMS Service & Kavenegar`, `PaymentServiceTest.php`, `ShippingMethod`, `Product Images`, `Catalog Model Tests`, `FooterTest`, `CatalogAdminTest`, `Product Page Tests`, `ScreenSmsDriverTest`, `SeoTest`, `App Boot & Security`, `SupportButtonTest`, `ErrorPagesTest.php`, `ProductVariant`, `Page`, `CartPageTest`, `AccountOrdersTest`, `FreeShippingMessageTest`, `OrdersAdminTest.php`, `ProductListPageTest`, `AdminPanelAccessTest`, `PagesTest`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **What connects `PendingPayment`, `Paid`, `Processing` to the rest of the system?**
  _40 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `ProductVariant` be split into smaller, more focused modules?**
  _Cohesion score 0.08576576576576576 - nodes in this community are weakly interconnected._
- **Why does `User` connect `AccountOrdersTest` to `ProductVariant`, `OtpLoginTest`, `CancelUnpaidOrdersCommand.php`, `OrderStatus`, `TestCase`, `Address Book Tests`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `CatalogAdminTest.php`, `Admin Tables`, `Payment Service Tests`, `DevSampleSeederTest.php`, `Admin Panel Access`, `PaymentServiceTest.php`, `ShippingMethod`, `FooterTest`, `ScreenSmsDriverTest`, `App Boot & Security`, `SmsException`, `ErrorPagesTest.php`, `Page`, `Category`, `HasPersianSlug`, `OrdersAdminTest.php`, `AdminPanelAccessTest`, `Order`?**
  _High betweenness centrality (0.100) - this node is a cross-community bridge._
- **Should `OtpLoginTest` be split into smaller, more focused modules?**
  _Cohesion score 0.08045977011494253 - nodes in this community are weakly interconnected._
- **Why does `Order` connect `Order` to `ProductVariant`, `CancelUnpaidOrdersCommand.php`, `OrderStatus`, `Filament Resources`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `web.php`, `Admin Tables`, `Payment Service Tests`, `Orders Admin Tests`, `Digits`, `Illuminate\Database\Eloquent\Factories\Factory`, `PaymentService`, `PaymentServiceTest.php`, `ShippingMethod`, `StoreStats`, `AccountOrdersTest`, `Category`, `PaymentController.php`, `OrdersAdminTest.php`, `SendOrderConfirmationSms`, `Illuminate\Database\Eloquent\Builder`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._

### Low-confidence Hints
_AMBIGUOUS edges — the extractor was unsure. Verify before acting on these._

- **What is the exact relationship between `Brand Sheet Preview (preview.html)` and `Self-hosted Fonts (not Google Fonts)`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._