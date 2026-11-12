# Graph Report - ilnia-shop  (2026-11-12)

## Corpus Check
- 240 files · ~52,978 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 24 file(s) not represented in the graph (top: (none) 17, .woff2 3, .css 2)

## Summary
- 1550 nodes · 3287 edges · 155 communities (52 shown, 103 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 218 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0d2353c4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Cart & Storefront Livewire
- Customer OTP Login
- CancelUnpaidOrdersCommand.php
- Order Status Lifecycle
- TestCase
- OrderForm.php
- Filament Resources
- Address Book Tests
- Illuminate\Database\Eloquent\Model
- Product
- web.php
- Admin Tables
- Order Relation Managers
- Payment Service Tests
- DevSampleSeederTest.php
- Frontend Build Tooling
- Orders Admin Tests
- Digits
- Storefront Controllers
- Illuminate\Database\Eloquent\Factories\Factory
- SMS Service & Kavenegar
- Admin Panel Access
- PaymentServiceTest.php
- Shipping Calculator
- Product Images
- Catalog Model Tests
- CatalogAdminTest.php
- Input Normalizers (Mobile, Weight)
- Product Page Tests
- Admin List Pages
- Product Variants & Cart Lines
- App Boot & Security
- README.md
- ARCHITECTURE.md
- PaymentService.php
- Product
- AppServiceProvider.php
- 0001_01_01_000000_create_users_table
- AddressesPage
- SECURITY.md
- CartPageTest
- StoreStats
- AccountOrdersTest
- Page
- ARCHITECTURE.md (2)
- CheckoutPage
- FreeShippingMessageTest
- Category
- ARCHITECTURE.md (3)
- ARCHITECTURE.md (4)
- IntegerInput
- composer.json
- composer.json (2)
- Filament\Tables\Table
- ProductListPageTest
- UserFactory.php
- app
- composer.json (3)
- AdminPanelAccessTest
- Filament\Schemas\Schema
- Illuminate\View\View
- SmsException
- composer.json (4)
- Illuminate\Database\Eloquent\Builder
- composer.json (5)
- ProductVariantFactory
- ErrorPagesTest.php
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
- ItemsRelationManager
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
- ARCHITECTURE.md (5)
- header
- show
- 500
- 503
- ARCHITECTURE.md (6)
- ARCHITECTURE.md (7)
- PaymentService
- .settleOpenPayments
- CartPage
- LogSmsDriver
- FooterTest

## God Nodes (most connected - your core abstractions)
1. `TestCase` - 71 edges
2. `OrderStatus` - 66 edges
3. `Order` - 59 edges
4. `ProductVariant` - 54 edges
5. `CartService` - 53 edges
6. `User` - 48 edges
7. `Product` - 44 edges
8. `PaymentServiceTest` - 33 edges
9. `OtpLoginTest` - 32 edges
10. `ShippingMethod` - 30 edges

## Surprising Connections (you probably didn't know these)
- `Tailwind Theme Colors (ink, gold, gold-deep, cream, paper, muted, line)` --semantically_similar_to--> `tokens.css (Tailwind v4 @theme)`  [INFERRED] [semantically similar]
  docs/DESIGN.md → brand/README.md
- `Later Backlog (blog, discount codes, real-buyer reviews, Basalam/Torob)` --conceptually_related_to--> `Honesty Rule in Site (no fake claims, reviews, stock photos)`  [INFERRED]
  docs/ROADMAP.md → AGENTS.md
- `Photo Placeholder (cream box with Ilnia mark)` --references--> `Logo Mark SVGs (logo-mark-gold / logo-mark-ink / favicon)`  [INFERRED]
  docs/DESIGN.md → brand/README.md
- `Shared Hosting Constraints (no Node, no Redis/Supervisor, cron-driven queue)` --rationale_for--> `Deployment Guide`  [INFERRED]
  AGENTS.md → docs/DEPLOY.md
- `Non-negotiable Security Rules` --conceptually_related_to--> `Security Checklist`  [INFERRED]
  AGENTS.md → docs/SECURITY.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Checkout and Payment Flow (cart -> order -> Zarinpal -> verify -> SMS)** — docs_architecture_cartservice, docs_architecture_orderpricing, docs_architecture_orderservice, docs_architecture_paymentservice, docs_architecture_payment_idempotency, docs_architecture_send_order_confirmation_sms, docs_architecture_smsservice [EXTRACTED 1.00]
- **Cron-driven Background Work on Shared Hosting** — docs_deploy_cron_schedule, docs_architecture_cancel_unpaid_orders, docs_architecture_send_order_confirmation_sms, docs_architecture_otpservice, agents_shared_hosting_constraints [EXTRACTED 1.00]
- **Brand Visual Identity Assets for the Site** — brand_readme_tokens_css, brand_readme_logo_mark_svgs, brand_readme_x_logo_component, docs_design_tailwind_theme, brand_preview_color_palette, public_fonts_readme_font_files [INFERRED 0.85]

## Communities (155 total, 103 thin omitted)

### Community 0 - "Cart & Storefront Livewire"
Cohesion: 0.10
Nodes (4): CartException, CartService, CartServiceTest, OrderServiceTest

### Community 1 - "Customer OTP Login"
Cohesion: 0.06
Nodes (18): OtpException, LoginPage, OtpService, {closure#1}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#2}() (+10 more)

### Community 3 - "Order Status Lifecycle"
Cohesion: 0.06
Nodes (20): OrderStatus, Cancelled, Delivered, NeedsAttention, Paid, PaymentFailed, PendingPayment, Processing (+12 more)

### Community 4 - "TestCase"
Cohesion: 0.10
Nodes (6): ListOrders, CartIcon, LayoutTest, LogoComponentTest, OrderPricingTest, TestCase

### Community 5 - "OrderForm.php"
Cohesion: 0.09
Nodes (19): {closure#4}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#10}(), {closure#2}(), {closure#3}() (+11 more)

### Community 6 - "Filament Resources"
Cohesion: 0.16
Nodes (7): CategoryResource, CreateCategory, OrderResource, CreateProduct, ProductResource, CreateShippingMethod, ShippingMethodResource

### Community 8 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.13
Nodes (4): Address, OrderItem, AddressFactory, OrderItemFactory

### Community 9 - "Product"
Cohesion: 0.21
Nodes (4): {closure#1}(), {closure#2}(), ProductImage, ProductImageFactory

### Community 10 - "web.php"
Cohesion: 0.13
Nodes (5): OrderController, Controller, ProductController, SeoController, POST /logout()

### Community 11 - "Admin Tables"
Cohesion: 0.15
Nodes (8): {closure#2}(), {closure#4}(), LowStock, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), OrdersToShip

### Community 12 - "Order Relation Managers"
Cohesion: 0.20
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), Jalali, JalaliTest

### Community 14 - "DevSampleSeederTest.php"
Cohesion: 0.07
Nodes (6): DatabaseSeeder, DevSampleSeeder, PageSeeder, ShippingMethodSeeder, DevSampleSeederTest, SeoTest

### Community 15 - "Frontend Build Tooling"
Cohesion: 0.09
Nodes (23): dependencies, vazirmatn, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+15 more)

### Community 17 - "Digits"
Cohesion: 0.14
Nodes (4): Digits, Weight, DigitsTest, WeightTest

### Community 18 - "Storefront Controllers"
Cohesion: 0.33
Nodes (3): HomeController, PageController, ShippingCalculator

### Community 19 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.18
Nodes (3): PaymentFactory, ProductFactory, ShippingMethodFactory

### Community 22 - "PaymentServiceTest.php"
Cohesion: 0.09
Nodes (10): SendOrderConfirmationSms, {closure#1}(), {closure#1}(), {closure#1}(), {closure#1}(), {closure#3}(), {closure#5}(), {closure#6}() (+2 more)

### Community 26 - "CatalogAdminTest.php"
Cohesion: 0.05
Nodes (7): EditCategory, EditOrder, EditPage, EditProduct, EditShippingMethod, CatalogAdminTest, PagesTest

### Community 29 - "Admin List Pages"
Cohesion: 0.15
Nodes (5): ListCategories, PageResource, ListPages, ListProducts, ListShippingMethods

### Community 30 - "Product Variants & Cart Lines"
Cohesion: 0.36
Nodes (3): {closure#1}(), ProductVariant, CartLine

### Community 32 - "README.md"
Cohesion: 0.20
Nodes (10): Brand Sheet Preview (preview.html), Brand Color Palette (ink, ink-soft, gold, gold-deep, honey, cream, paper, muted), Brand Assets README, brand.json (name, slogans, colors, paths), Logo Mark SVGs (logo-mark-gold / logo-mark-ink / favicon), tokens.css (Tailwind v4 @theme), <x-logo /> Blade Component, Design Doc (+2 more)

### Community 33 - "ARCHITECTURE.md"
Cohesion: 0.18
Nodes (12): Project .env Variables (KAVENEGAR_*, SMS_DRIVER, ZARINPAL_*), ilnia:cancel-unpaid-orders Command, Gateway Disabled Mode (PaymentService::isEnabled), OtpService (5-digit code, 2 min, rate limits), PaymentService (Zarinpal REST v4 via Laravel Http), SendOrderConfirmationSms Job, SmsService Interface (LogSmsDriver / KavenegarSmsDriver), PaymentService::toRial() (Toman x10) (+4 more)

### Community 35 - "Product"
Cohesion: 0.18
Nodes (6): {closure#2}(), {closure#3}(), {closure#4}(), {closure#6}(), {closure#1}(), Product

### Community 36 - "AppServiceProvider.php"
Cohesion: 0.21
Nodes (3): {closure#1}(), KavenegarSmsDriver, GET /__ip()

### Community 37 - "0001_01_01_000000_create_users_table"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 39 - "SECURITY.md"
Cohesion: 0.27
Nodes (12): Architecture Doc, Architecture Decisions Log, Deployment Guide, Document Root Methods (A: public/, B: public_html with usePublicPath), Minimum Host PHP 8.4.1 / config.platform.php, PUBLIC_STORAGE_PATH (no-symlink storage), Phase 4: Launch Preparation (open: code review, host PHP check, real gateway, Kavenegar templates), Security Checklist (+4 more)

### Community 43 - "Page"
Cohesion: 0.23
Nodes (5): {closure#3}(), {closure#2}(), {closure#3}(), {closure#4}(), Page

### Community 44 - "ARCHITECTURE.md (2)"
Cohesion: 0.24
Nodes (8): Fixed Tech Stack (PHP 8.5 + Laravel + Livewire + Filament + Tailwind), OrderPricing::quote() -> OrderTotals, OrderService::createFromCart, ShippingCalculator (weight-based free shipping >= 4000g, 60,000 Toman post), Roadmap, Later Backlog (blog, discount codes, real-buyer reviews, Basalam/Torob), Phase 3: Order and Payment, User Answers 2026-11-11 (PHP, SMS, shipping, product, price, gateway, host)

### Community 47 - "Category"
Cohesion: 0.16
Nodes (4): Category, {closure#1}(), HasPersianSlug, CategoryFactory

### Community 48 - "ARCHITECTURE.md (3)"
Cohesion: 0.22
Nodes (6): AGENTS.md (Agent Guide), ilnia-shop CLAUDE.md (includes AGENTS.md), Content Pages (about, contact, shipping, terms) via PageSeeder drafts, DevSampleSeeder (local-only, labeled sample), SEO (layout meta, OG, schema.org Product in IRR, sitemap, robots), Photo Placeholder (cream box with Ilnia mark)

### Community 49 - "ARCHITECTURE.md (4)"
Cohesion: 0.22
Nodes (8): Filament Admin Panel (/admin), App\Filament\Forms\IntegerInput, App\Support\Jalali (IntlDateFormatter), needs_attention Order State, Order Number Format IL-yyMMdd-XXXX, OrderStatus Enum (pending_payment -> paid -> processing -> shipped -> delivered; payment_failed, cancelled, needs_attention), Hosted Fonts README, admin.css (Filament panel fonts, loaded by AdminPanelProvider)

### Community 50 - "IntegerInput"
Cohesion: 0.19
Nodes (3): {closure#3}(), SecurityHeaders, {closure#1}()

### Community 51 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 52 - "composer.json (2)"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 53 - "Filament\Tables\Table"
Cohesion: 0.11
Nodes (8): CategoriesTable, {closure#2}(), {closure#3}(), {closure#4}(), OrdersTable, ProductsTable, {closure#1}(), ShippingMethodsTable

### Community 56 - "app"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 57 - "composer.json (3)"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 59 - "Filament\Schemas\Schema"
Cohesion: 0.12
Nodes (5): IntegerInput, CategoryForm, {closure#2}(), ProductForm, ShippingMethodForm

### Community 62 - "composer.json (4)"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 64 - "composer.json (5)"
Cohesion: 0.33
Nodes (6): require, filament/filament, laravel/framework, laravel/tinker, livewire/livewire, php

### Community 66 - "ErrorPagesTest.php"
Cohesion: 0.14
Nodes (3): ErrorPagesTest, LivewireSetupTest, SetupCheckComponent

### Community 67 - "CheckoutPage.php"
Cohesion: 0.21
Nodes (3): OrderPricing, OrderService, OrderTotals

### Community 68 - "User"
Cohesion: 0.16
Nodes (3): {closure#1}(), {closure#2}(), User

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
Cohesion: 0.27
Nodes (3): {closure#11}(), PaymentController, Order

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

### Community 96 - "ARCHITECTURE.md (5)"
Cohesion: 0.67
Nodes (3): CartService (session-based cart), Data Model (users, otp_codes, categories, products, variants, orders, payments...), Site Pages v1 (home, products, product, cart, checkout, result, account, about, contact)

## Ambiguous Edges - Review These
- `Brand Sheet Preview (preview.html)` → `Self-hosted Fonts (not Google Fonts)`  [AMBIGUOUS]
  brand/preview.html · relation: conceptually_related_to

## Knowledge Gaps
- **43 isolated node(s):** `PendingPayment`, `Paid`, `Processing`, `Shipped`, `Delivered` (+38 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 489 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **103 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `TestCase` connect `TestCase` to `Cart & Storefront Livewire`, `Customer OTP Login`, `Order Status Lifecycle`, `OrderForm.php`, `Address Book Tests`, `Admin Tables`, `Order Relation Managers`, `Payment Service Tests`, `DevSampleSeederTest.php`, `Orders Admin Tests`, `SMS Service & Kavenegar`, `PaymentServiceTest.php`, `Shipping Calculator`, `Product Images`, `Catalog Model Tests`, `FooterTest`, `CatalogAdminTest.php`, `Product Page Tests`, `App Boot & Security`, `AppServiceProvider.php`, `CartPageTest`, `AccountOrdersTest`, `FreeShippingMessageTest`, `ProductListPageTest`, `AdminPanelAccessTest`, `ErrorPagesTest.php`, `CheckoutPage.php`?**
  _High betweenness centrality (0.133) - this node is a cross-community bridge._
- **What connects `PendingPayment`, `Paid`, `Processing` to the rest of the system?**
  _43 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Cart & Storefront Livewire` be split into smaller, more focused modules?**
  _Cohesion score 0.10116550116550116 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User` to `Cart & Storefront Livewire`, `Customer OTP Login`, `CancelUnpaidOrdersCommand.php`, `Order Status Lifecycle`, `TestCase`, `Address Book Tests`, `Illuminate\Database\Eloquent\Model`, `Admin Tables`, `Payment Service Tests`, `DevSampleSeederTest.php`, `Admin Panel Access`, `PaymentServiceTest.php`, `CatalogAdminTest.php`, `FooterTest`, `PaymentService.php`, `AppServiceProvider.php`, `AccountOrdersTest`, `CheckoutPage`, `IntegerInput`, `UserFactory.php`, `AdminPanelAccessTest`, `ErrorPagesTest.php`, `CheckoutPage.php`?**
  _High betweenness centrality (0.089) - this node is a cross-community bridge._
- **Should `Customer OTP Login` be split into smaller, more focused modules?**
  _Cohesion score 0.05868544600938967 - nodes in this community are weakly interconnected._
- **Why does `Order` connect `Illuminate\Http\Request` to `Cart & Storefront Livewire`, `Order Status Lifecycle`, `TestCase`, `OrderForm.php`, `Filament Resources`, `Illuminate\Database\Eloquent\Model`, `web.php`, `Admin Tables`, `Payment Service Tests`, `Orders Admin Tests`, `Digits`, `Illuminate\Database\Eloquent\Factories\Factory`, `PaymentServiceTest.php`, `.settleOpenPayments`, `PaymentService`, `PaymentService.php`, `StoreStats`, `AccountOrdersTest`, `CheckoutPage.php`, `User`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._

### Low-confidence Hints
_AMBIGUOUS edges — the extractor was unsure. Verify before acting on these._

- **What is the exact relationship between `Brand Sheet Preview (preview.html)` and `Self-hosted Fonts (not Google Fonts)`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._