# Graph Report - ilnia-shop  (2026-11-17)

## Corpus Check
- 239 files · ~48,924 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 24 file(s) not represented in the graph (top: (none) 17, .woff2 3, .css 2)

## Summary
- 1521 nodes · 3232 edges · 160 communities (47 shown, 113 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 190 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `28c505d1`
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
- Illuminate\Database\Eloquent\Model
- PaymentStatus
- web.php
- Admin Tables
- Order Relation Managers
- Payment Service Tests
- DevSampleSeederTest.php
- Frontend Build Tooling
- Orders Admin Tests
- Digits
- AddressesPageTest
- Illuminate\Database\Eloquent\Factories\Factory
- SMS Service & Kavenegar
- Admin Panel Access
- PaymentServiceTest.php
- Shipping Calculator
- Product Images
- Catalog Model Tests
- CatalogAdminTest
- Input Normalizers (Mobile, Weight)
- Product Page Tests
- Admin List Pages
- SupportButtonTest
- App Boot & Security
- AGENTS.md (Agent Guide)
- PageResource.php
- SmsException
- Product
- LivewireSetupTest.php
- 0001_01_01_000000_create_users_table
- AddressesPage
- DashboardTest
- CartPageTest
- StoreStats
- AccountOrdersTest
- Category
- Filament\Resources\Pages\CreateRecord
- CancelUnpaidOrdersTest.php
- FreeShippingMessageTest
- HasPersianSlug
- Filament\Resources\Pages\EditRecord
- KavenegarSmsDriver
- IntegerInput
- composer.json
- composer.json (2)
- OrdersAdminTest.php
- ProductListPageTest
- UserFactory.php
- app
- composer.json (3)
- AdminPanelAccessTest
- Filament\Schemas\Schema
- Livewire\Component
- SendOrderConfirmationSms
- composer.json (4)
- Illuminate\Database\Eloquent\Builder
- composer.json (5)
- ProductVariantFactory
- ErrorPagesTest
- CheckoutPage.php
- User
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
- Illuminate\Http\Request
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
- CheckoutPage
- PageResource
- PaymentService
- BottomNavTest
- ScreenSmsDriver
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

## Communities (160 total, 113 thin omitted)

### Community 0 - "ProductVariant"
Cohesion: 0.06
Nodes (10): CartException, CartPage, {closure#1}(), ProductPurchase, {closure#2}(), ProductVariant, CartLine, CartService (+2 more)

### Community 1 - "OtpLoginTest"
Cohesion: 0.08
Nodes (16): LoginPage, OtpService, {closure#1}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#2}(), {closure#3}() (+8 more)

### Community 3 - "OrderStatus"
Cohesion: 0.12
Nodes (12): OrderStatus, Cancelled, Delivered, NeedsAttention, Paid, PaymentFailed, PendingPayment, Processing (+4 more)

### Community 4 - "TestCase"
Cohesion: 0.12
Nodes (6): EditPage, CartIcon, LayoutTest, LogoComponentTest, OrderPricingTest, TestCase

### Community 5 - "OrderForm.php"
Cohesion: 0.12
Nodes (15): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#10}(), {closure#2}(), {closure#3}(), {closure#4}() (+7 more)

### Community 6 - "Filament Resources"
Cohesion: 0.19
Nodes (6): CategoryResource, CategoriesTable, ProductResource, ProductsTable, ShippingMethodResource, ShippingMethodsTable

### Community 8 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (5): Address, OrderItem, {closure#1}(), {closure#2}(), ProductImage

### Community 9 - "PaymentStatus"
Cohesion: 0.20
Nodes (6): PaymentStatus, Cancelled, Failed, Paid, Pending, CancelUnpaidOrdersTest

### Community 10 - "web.php"
Cohesion: 0.15
Nodes (6): OrderController, Controller, HomeController, PageController, ProductController, POST /logout()

### Community 11 - "Admin Tables"
Cohesion: 0.22
Nodes (4): {closure#2}(), {closure#4}(), LowStock, OrdersToShip

### Community 12 - "Order Relation Managers"
Cohesion: 0.15
Nodes (9): {closure#1}(), {closure#2}(), {closure#3}(), {closure#5}(), {closure#6}(), {closure#2}(), {closure#3}(), Jalali (+1 more)

### Community 14 - "DevSampleSeederTest.php"
Cohesion: 0.11
Nodes (4): DatabaseSeeder, DevSampleSeeder, PageSeeder, DevSampleSeederTest

### Community 15 - "Frontend Build Tooling"
Cohesion: 0.09
Nodes (23): dependencies, vazirmatn, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+15 more)

### Community 19 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.10
Nodes (7): AddressFactory, CategoryFactory, OrderItemFactory, PaymentFactory, ProductFactory, ProductImageFactory, ShippingMethodFactory

### Community 22 - "PaymentServiceTest.php"
Cohesion: 0.11
Nodes (8): {closure#1}(), {closure#1}(), {closure#1}(), {closure#3}(), {closure#5}(), {closure#6}(), {closure#3}(), {closure#4}()

### Community 23 - "Shipping Calculator"
Cohesion: 0.21
Nodes (3): ShippingMethod, ShippingCalculator, ShippingCalculatorTest

### Community 27 - "Input Normalizers (Mobile, Weight)"
Cohesion: 0.17
Nodes (3): Weight, MobileTest, WeightTest

### Community 29 - "Admin List Pages"
Cohesion: 0.19
Nodes (5): ListCategories, ListOrders, ListPages, ListProducts, ListShippingMethods

### Community 31 - "App Boot & Security"
Cohesion: 0.11
Nodes (3): AppServiceProvider, GET /__ip(), SecurityTest

### Community 32 - "AGENTS.md (Agent Guide)"
Cohesion: 0.11
Nodes (14): AGENTS.md (Agent Guide), Project .env Variables (KAVENEGAR_*, SMS_DRIVER, ZARINPAL_*), Fixed Tech Stack (PHP 8.5 + Laravel + Livewire + Filament + Tailwind), Brand Sheet Preview (preview.html), Brand Color Palette (ink, ink-soft, gold, gold-deep, honey, cream, paper, muted), Brand Assets README, brand.json (name, slogans, colors, paths), Logo Mark SVGs (logo-mark-gold / logo-mark-ink / favicon) (+6 more)

### Community 33 - "PageResource.php"
Cohesion: 0.16
Nodes (4): {closure#2}(), {closure#2}(), {closure#4}(), {closure#1}()

### Community 35 - "Product"
Cohesion: 0.13
Nodes (6): {closure#2}(), {closure#3}(), {closure#4}(), {closure#6}(), {closure#1}(), Product

### Community 37 - "0001_01_01_000000_create_users_table"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 43 - "Category"
Cohesion: 0.21
Nodes (4): {closure#2}(), {closure#3}(), SeoController, Category

### Community 44 - "Filament\Resources\Pages\CreateRecord"
Cohesion: 0.43
Nodes (3): CreateCategory, CreateProduct, CreateShippingMethod

### Community 48 - "Filament\Resources\Pages\EditRecord"
Cohesion: 0.14
Nodes (4): EditCategory, EditOrder, EditProduct, EditShippingMethod

### Community 50 - "IntegerInput"
Cohesion: 0.12
Nodes (6): {closure#3}(), {closure#4}(), SecurityHeaders, {closure#1}(), Toman, TomanTest

### Community 51 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 52 - "composer.json (2)"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 53 - "OrdersAdminTest.php"
Cohesion: 0.22
Nodes (3): ItemsRelationManager, PaymentsRelationManager, ShippingMethodSeeder

### Community 56 - "app"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 57 - "composer.json (3)"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 59 - "Filament\Schemas\Schema"
Cohesion: 0.15
Nodes (4): IntegerInput, CategoryForm, ProductForm, ShippingMethodForm

### Community 62 - "composer.json (4)"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 63 - "Illuminate\Database\Eloquent\Builder"
Cohesion: 0.14
Nodes (4): {closure#3}(), {closure#4}(), OtpCode, Page

### Community 64 - "composer.json (5)"
Cohesion: 0.33
Nodes (6): require, filament/filament, laravel/framework, laravel/tinker, livewire/livewire, php

### Community 67 - "CheckoutPage.php"
Cohesion: 0.20
Nodes (4): OrderException, OrderPricing, OrderService, OrderTotals

### Community 69 - "addresses-page"
Cohesion: 0.40
Nodes (4): cancel, create, delete({{ $address->id }}), edit({{ $address->id }})

### Community 70 - "composer.json (6)"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 79 - "product-purchase"
Cohesion: 0.50
Nodes (3): addToCart, decrement, increment

### Community 80 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (7): {closure#11}(), {closure#1}(), {closure#4}(), PaymentController, {closure#1}(), Order, {closure#1}()

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
Cohesion: 0.16
Nodes (4): CancelUnpaidOrdersCommand, PaymentException, Payment, PaymentService

### Community 153 - "ScreenSmsDriver"
Cohesion: 0.17
Nodes (4): {closure#1}(), LogSmsDriver, ScreenSmsDriver, Mobile

## Ambiguous Edges - Review These
- `Brand Sheet Preview (preview.html)` → `Self-hosted Fonts (not Google Fonts)`  [AMBIGUOUS]
  brand/preview.html · relation: conceptually_related_to

## Knowledge Gaps
- **40 isolated node(s):** `PendingPayment`, `Paid`, `Processing`, `Shipped`, `Delivered` (+35 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 498 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **113 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `TestCase` connect `TestCase` to `ProductVariant`, `OtpLoginTest`, `OrderStatus`, `Address Book Tests`, `PaymentStatus`, `Admin Tables`, `Order Relation Managers`, `Payment Service Tests`, `DevSampleSeederTest.php`, `Orders Admin Tests`, `AddressesPageTest`, `SMS Service & Kavenegar`, `PaymentServiceTest.php`, `BottomNavTest`, `Product Images`, `Catalog Model Tests`, `CatalogAdminTest`, `FooterTest`, `Shipping Calculator`, `Product Page Tests`, `ScreenSmsDriver`, `ScreenSmsDriverTest`, `App Boot & Security`, `SeoTest`, `SupportButtonTest`, `.setUp`, `LivewireSetupTest.php`, `DashboardTest`, `CartPageTest`, `AccountOrdersTest`, `Category`, `CancelUnpaidOrdersTest.php`, `FreeShippingMessageTest`, `Filament\Resources\Pages\EditRecord`, `IntegerInput`, `OrdersAdminTest.php`, `ProductListPageTest`, `AdminPanelAccessTest`, `ErrorPagesTest`, `CheckoutPage.php`, `User`, `PagesTest`?**
  _High betweenness centrality (0.145) - this node is a cross-community bridge._
- **What connects `PendingPayment`, `Paid`, `Processing` to the rest of the system?**
  _40 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `ProductVariant` be split into smaller, more focused modules?**
  _Cohesion score 0.05996212918156953 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User` to `ProductVariant`, `OtpLoginTest`, `CancelUnpaidOrdersCommand.php`, `OrderStatus`, `TestCase`, `Address Book Tests`, `Illuminate\Database\Eloquent\Model`, `Admin Tables`, `Payment Service Tests`, `DevSampleSeederTest.php`, `AddressesPageTest`, `Illuminate\Database\Eloquent\Factories\Factory`, `Admin Panel Access`, `PaymentServiceTest.php`, `ScreenSmsDriver`, `FooterTest`, `App Boot & Security`, `SmsException`, `Product`, `AccountOrdersTest`, `CancelUnpaidOrdersTest.php`, `Filament\Resources\Pages\EditRecord`, `IntegerInput`, `OrdersAdminTest.php`, `UserFactory.php`, `AdminPanelAccessTest`, `ErrorPagesTest`, `CheckoutPage.php`, `CheckoutPage`?**
  _High betweenness centrality (0.105) - this node is a cross-community bridge._
- **Should `OtpLoginTest` be split into smaller, more focused modules?**
  _Cohesion score 0.08045977011494253 - nodes in this community are weakly interconnected._
- **Why does `Order` connect `Illuminate\Http\Request` to `ProductVariant`, `OrderStatus`, `OrderForm.php`, `Filament Resources`, `Illuminate\Database\Eloquent\Model`, `PaymentStatus`, `web.php`, `Admin Tables`, `Payment Service Tests`, `Orders Admin Tests`, `Digits`, `Illuminate\Database\Eloquent\Factories\Factory`, `PaymentService`, `PaymentServiceTest.php`, `DashboardTest`, `StoreStats`, `AccountOrdersTest`, `CancelUnpaidOrdersTest.php`, `OrdersAdminTest.php`, `SendOrderConfirmationSms`, `CheckoutPage.php`, `User`?**
  _High betweenness centrality (0.065) - this node is a cross-community bridge._

### Low-confidence Hints
_AMBIGUOUS edges — the extractor was unsure. Verify before acting on these._

- **What is the exact relationship between `Brand Sheet Preview (preview.html)` and `Self-hosted Fonts (not Google Fonts)`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._