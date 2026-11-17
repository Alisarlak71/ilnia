# Graph Report - ilnia-shop  (2026-11-17)

## Corpus Check
- 277 files · ~113,045 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 35 file(s) not represented in the graph (top: (none) 18, .woff2 10, .css 4)

## Summary
- 7067 nodes · 21871 edges · 221 communities (129 shown, 92 thin omitted)
- Extraction: 91% EXTRACTED · 9% INFERRED · 0% AMBIGUOUS · INFERRED: 2006 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7368de91`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- ProductVariant
- OtpLoginTest
- code-editor.js
- OrderStatus
- rich-editor.js
- OrderForm.php
- Filament Resources
- Address Book Tests
- Product
- components/chart.js
- update
- Admin Tables
- Order Relation Managers
- Payment Service Tests
- stat/chart.js
- Frontend Build Tooling
- OrdersAdminTest
- fromObject
- TestCase
- t
- support.js
- Admin Panel Access
- SmsService
- ShippingMethod
- addProseMirrorPlugins
- resolve
- CatalogAdminTest
- draw
- Product Page Tests
- Admin List Pages
- SupportButtonTest
- constructor
- AGENTS.md (Agent Guide)
- Filament\Tables\Table
- markdown-editor.js
- of
- sr
- 0001_01_01_000000_create_users_table
- n
- _update
- ProductVariant
- e
- User
- find
- yd
- facet
- file-upload.js
- slice
- Filament\Resources\Pages\EditRecord
- advance
- .parse
- composer.json
- composer.json (2)
- get
- D
- copy
- iy
- composer.json (3)
- gc
- Filament\Schemas\Schema
- dr
- e
- composer.json (4)
- tabs.js
- composer.json (5)
- add
- notifications.js
- _update
- columns/select.js
- addresses-page
- composer.json (6)
- 0001_01_01_000001_create_cache_table
- 2026_11_11_231149_add_is_admin_to_users_table
- 2026_11_11_232034_create_categories_table
- 2026_11_11_232036_create_products_table
- 2026_11_11_234636_add_storage_instructions_to_products_table
- 2026_11_12_000958_add_mobile_to_users_table
- d
- product-purchase
- split
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
- echo.js
- header.blade.php
- show
- 500
- 503
- updateElements
- PageResource
- Order
- parse
- tables.js
- lineAt
- y
- I
- te
- components/select.js
- r
- bottom-nav.blade.php
- parse
- S
- e
- ei
- init
- finish
- De
- renderOptions
- di
- n
- filament/app.js
- eq
- constructor
- dn
- getContext
- date-time-picker.js
- apply
- renderOptions
- go
- E
- pt
- A
- oS
- constructor
- _notify
- Y
- va
- _notify
- updateSelectedDisplay
- gu
- color-picker.js
- fn
- px
- addEventListener
- toString
- fn
- _r
- jh
- actions/actions.js
- Fe
- schemas.js
- builder.js
- شاخهٔ vercel-demo (فقط برای نمایش)
- components/actions.js
- f
- repeater.js
- vercel.json
- toggle.js

## God Nodes (most connected - your core abstractions)
1. `update()` - 146 edges
2. `constructor()` - 137 edges
3. `resolve()` - 99 edges
4. `y()` - 88 edges
5. `_update()` - 85 edges
6. `node()` - 80 edges
7. `constructor()` - 79 edges
8. `e()` - 79 edges
9. `te()` - 75 edges
10. `TestCase` - 75 edges

## Surprising Connections (you probably didn't know these)
- `شاخهٔ vercel-demo (فقط برای نمایش)` --references--> `main()`  [INFERRED]
  VERCEL-DEMO.md → public/js/filament/forms/components/code-editor.js
- `OrderServiceTest` --references--> `Address`  [EXTRACTED]
  tests/Feature/OrderServiceTest.php → app/Models/Address.php
- `PaymentServiceTest` --references--> `Order`  [EXTRACTED]
  tests/Feature/PaymentServiceTest.php → app/Models/Order.php
- `CancelUnpaidOrdersTest` --references--> `ProductVariant`  [EXTRACTED]
  tests/Feature/CancelUnpaidOrdersTest.php → app/Models/ProductVariant.php
- `PaymentResultPageTest` --references--> `ProductVariant`  [EXTRACTED]
  tests/Feature/PaymentResultPageTest.php → app/Models/ProductVariant.php

## Import Cycles
- None detected.

## Communities (221 total, 92 thin omitted)

### Community 0 - "ProductVariant"
Cohesion: 0.10
Nodes (4): CartException, CartService, CartServiceTest, OrderServiceTest

### Community 1 - "OtpLoginTest"
Cohesion: 0.08
Nodes (17): OtpException, LoginPage, OtpService, {closure#1}(), {closure#10}(), {closure#11}(), {closure#12}(), {closure#2}() (+9 more)

### Community 2 - "code-editor.js"
Cohesion: 0.01
Nodes (133): Ac(), acceptToken(), addActive(), addChanges(), addSelection(), ag(), Ar(), Blockquote() (+125 more)

### Community 3 - "OrderStatus"
Cohesion: 0.05
Nodes (22): OrderStatus, Cancelled, Delivered, NeedsAttention, Paid, PaymentFailed, PendingPayment, Processing (+14 more)

### Community 4 - "rich-editor.js"
Cohesion: 0.01
Nodes (189): addHackNode(), addNode(), addTextblockHacks(), ah(), ak(), applyAspectRatio(), applyConstraints(), atEnd() (+181 more)

### Community 5 - "OrderForm.php"
Cohesion: 0.11
Nodes (17): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#10}(), {closure#2}(), {closure#3}(), {closure#4}() (+9 more)

### Community 6 - "Filament Resources"
Cohesion: 0.16
Nodes (7): CategoryResource, CreateCategory, OrderResource, CreateProduct, ProductResource, CreateShippingMethod, ShippingMethodResource

### Community 8 - "Product"
Cohesion: 0.03
Nodes (28): {closure#3}(), {closure#2}(), {closure#3}(), {closure#4}(), SeoController, Address, Category, HasPersianSlug (+20 more)

### Community 9 - "components/chart.js"
Cohesion: 0.01
Nodes (124): abutsStart(), acquireContext(), addControllers(), addPlugins(), addScales(), Bt(), Cl(), contains() (+116 more)

### Community 10 - "update"
Cohesion: 0.03
Nodes (137): add(), addChunk(), addEventListener(), addInfoPane(), addInner(), addWindowListeners(), adjust(), annotation() (+129 more)

### Community 11 - "Admin Tables"
Cohesion: 0.15
Nodes (8): {closure#2}(), {closure#4}(), LowStock, {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), OrdersToShip

### Community 12 - "Order Relation Managers"
Cohesion: 0.20
Nodes (5): {closure#1}(), {closure#2}(), {closure#3}(), Jalali, JalaliTest

### Community 14 - "stat/chart.js"
Cohesion: 0.03
Nodes (95): th(), addControllers(), addElements(), addPlugins(), addScales(), ao(), applyStack(), ba() (+87 more)

### Community 15 - "Frontend Build Tooling"
Cohesion: 0.09
Nodes (23): dependencies, vazirmatn, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+15 more)

### Community 16 - "OrdersAdminTest"
Cohesion: 0.05
Nodes (3): EditOrder, CatalogModelsTest, OrdersAdminTest

### Community 17 - "fromObject"
Cohesion: 0.03
Nodes (131): ae(), after(), Ag(), Am(), bd(), before(), bm(), buildFormatParser() (+123 more)

### Community 18 - "TestCase"
Cohesion: 0.03
Nodes (16): CartIcon, PageSeeder, AdminPanelAccessTest, BottomNavTest, DevSampleSeederTest, ErrorPagesTest, FooterTest, HomePageTest (+8 more)

### Community 19 - "t"
Cohesion: 0.03
Nodes (125): $0(), aa(), addCompletion(), addCompletions(), addNamespace(), addNamespaceObject(), aO(), atLastNode() (+117 more)

### Community 20 - "support.js"
Cohesion: 0.03
Nodes (103): Ac(), An(), As(), au(), Bo(), cc(), Ce(), cr() (+95 more)

### Community 22 - "SmsService"
Cohesion: 0.03
Nodes (22): OrderException, SmsException, SendOrderConfirmationSms, AppServiceProvider, {closure#1}(), KavenegarSmsDriver, LogSmsDriver, ScreenSmsDriver (+14 more)

### Community 23 - "ShippingMethod"
Cohesion: 0.03
Nodes (21): {closure#6}(), HomeController, PageController, ProductController, AddressesPage, CartPage, CheckoutPage, AddressForm (+13 more)

### Community 24 - "addProseMirrorPlugins"
Cohesion: 0.03
Nodes (118): $1(), addGlobalAttributes(), addMark(), addNodeView(), addOptions(), addPasteRules(), addProseMirrorPlugins(), An() (+110 more)

### Community 25 - "resolve"
Cohesion: 0.06
Nodes (117): addKeyboardShortcuts(), after(), al(), Au(), ay(), before(), between(), blockRange() (+109 more)

### Community 26 - "CatalogAdminTest"
Cohesion: 0.05
Nodes (3): CatalogAdminTest, PagesTest, ScreenSmsDriverTest

### Community 27 - "draw"
Cohesion: 0.04
Nodes (112): adjustHitBoxes(), afterDraw(), ar(), Be(), _calculateBarValuePixels(), calculateLabelRotation(), _calculatePadding(), clear() (+104 more)

### Community 29 - "Admin List Pages"
Cohesion: 0.19
Nodes (5): ListCategories, ListOrders, ListPages, ListProducts, ListShippingMethods

### Community 31 - "constructor"
Cohesion: 0.03
Nodes (112): add(), addExtensions(), applyChangedRangesRecompute(), applyInitialSize(), b0(), Bl(), buildFullSet(), buildMergedSet() (+104 more)

### Community 32 - "AGENTS.md (Agent Guide)"
Cohesion: 0.11
Nodes (14): AGENTS.md (Agent Guide), Project .env Variables (KAVENEGAR_*, SMS_DRIVER, ZARINPAL_*), Fixed Tech Stack (PHP 8.5 + Laravel + Livewire + Filament + Tailwind), Brand Sheet Preview (preview.html), Brand Color Palette (ink, ink-soft, gold, gold-deep, honey, cream, paper, muted), Brand Assets README, brand.json (name, slogans, colors, paths), Logo Mark SVGs (logo-mark-gold / logo-mark-ink / favicon) (+6 more)

### Community 33 - "Filament\Tables\Table"
Cohesion: 0.07
Nodes (13): CategoriesTable, {closure#2}(), ItemsRelationManager, PaymentsRelationManager, {closure#2}(), {closure#4}(), OrdersTable, {closure#2}() (+5 more)

### Community 34 - "markdown-editor.js"
Cohesion: 0.05
Nodes (99): af(), ai(), ao(), Ba(), Bf(), bl(), cd(), ce() (+91 more)

### Community 35 - "of"
Cohesion: 0.03
Nodes (102): active(), al(), l(), baseTheme(), Bh(), bi(), blockTiles(), blur() (+94 more)

### Community 36 - "sr"
Cohesion: 0.05
Nodes (94): Ae(), ar(), Be(), bt(), Ce(), De(), _e(), er() (+86 more)

### Community 37 - "0001_01_01_000000_create_users_table"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 38 - "n"
Cohesion: 0.05
Nodes (87): $0(), a0(), accepts(), addNodeMark(), ao(), ax(), bi(), changedRange() (+79 more)

### Community 39 - "_update"
Cohesion: 0.04
Nodes (92): addBox(), addElements(), afterBuildTicks(), afterCalculateLabelRotation(), afterDataLimits(), afterFit(), afterSetDimensions(), afterTickToLabelConversion() (+84 more)

### Community 40 - "ProductVariant"
Cohesion: 0.08
Nodes (6): {closure#1}(), ProductPurchase, ProductVariant, CartLine, CartPageTest, FreeShippingMessageTest

### Community 41 - "e"
Cohesion: 0.07
Nodes (86): Ac(), addCommands(), ag(), Ar(), At(), Bc(), c0(), cC() (+78 more)

### Community 42 - "User"
Cohesion: 0.05
Nodes (5): MakeAdminCommand, {closure#1}(), User, UserFactory, AccountOrdersTest

### Community 43 - "find"
Cohesion: 0.04
Nodes (84): activateHover(), addRange(), baseDirAt(), bd(), bidiIn(), bidiSpans(), bidiSpansAt(), bP() (+76 more)

### Community 44 - "yd"
Cohesion: 0.08
Nodes (78): _a(), Ae(), ar(), as(), Bc(), a(), i(), l() (+70 more)

### Community 45 - "facet"
Cohesion: 0.04
Nodes (77): accept(), addToSet(), Ah(), applyTransaction(), asSingle(), between(), cd(), changeByRange() (+69 more)

### Community 46 - "file-upload.js"
Cohesion: 0.04
Nodes (54): hc(), Id(), Ae(), Al(), Be(), Bp(), c(), clickPercent() (+46 more)

### Community 47 - "slice"
Cohesion: 0.04
Nodes (73): be(), Bg(), charCategorizer(), n(), createSet(), De(), decompose(), decomposeLeft() (+65 more)

### Community 48 - "Filament\Resources\Pages\EditRecord"
Cohesion: 0.24
Nodes (3): EditCategory, EditProduct, EditShippingMethod

### Community 49 - "advance"
Cohesion: 0.05
Nodes (69): addActions(), advance(), advanceFully(), advanceStack(), allActions(), allows(), apply(), break() (+61 more)

### Community 50 - ".parse"
Cohesion: 0.18
Nodes (4): {closure#3}(), SecurityHeaders, {closure#1}(), TomanTest

### Community 51 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 52 - "composer.json (2)"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 53 - "get"
Cohesion: 0.05
Nodes (67): addBlockWidget(), addBreak(), addComposition(), addDelimiter(), addInlineWidget(), addLine(), addLineStart(), addLineStartIfNotCovered() (+59 more)

### Community 54 - "D"
Cohesion: 0.10
Nodes (60): $a(), Ar(), C(), ca(), D(), Cn(), te(), D() (+52 more)

### Community 55 - "copy"
Cohesion: 0.06
Nodes (62): ad(), addInner(), af(), allowsMarks(), append(), Bg(), Bo(), cf() (+54 more)

### Community 56 - "iy"
Cohesion: 0.06
Nodes (63): addAll(), addDOM(), addElement(), addElementByRule(), addInputRules(), addStoredMark(), addTextNode(), addToSet() (+55 more)

### Community 57 - "composer.json (3)"
Cohesion: 0.25
Nodes (8): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, phpunit/phpunit

### Community 58 - "gc"
Cohesion: 0.10
Nodes (63): apply(), b(), bc(), Bs(), bt(), we(), Et(), gc() (+55 more)

### Community 59 - "Filament\Schemas\Schema"
Cohesion: 0.10
Nodes (6): {closure#4}(), IntegerInput, CategoryForm, OrderForm, ProductForm, ShippingMethodForm

### Community 60 - "dr"
Cohesion: 0.12
Nodes (60): Be(), br(), De(), dr(), ce(), cn(), Dn(), $e() (+52 more)

### Community 61 - "e"
Cohesion: 0.05
Nodes (61): apply(), applyStack(), average(), Ba(), beforeDatasetsDraw(), beforeDraw(), bi(), bu() (+53 more)

### Community 62 - "composer.json (4)"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 63 - "tabs.js"
Cohesion: 0.11
Nodes (54): at(), B(), be(), bt(), ce(), Ct(), D(), de() (+46 more)

### Community 64 - "composer.json (5)"
Cohesion: 0.33
Nodes (6): require, filament/filament, laravel/framework, laravel/tinker, livewire/livewire, php

### Community 65 - "add"
Cohesion: 0.05
Nodes (57): add(), alpha(), beforeDatasetDraw(), Ch(), co(), color(), darken(), desaturate() (+49 more)

### Community 66 - "notifications.js"
Cohesion: 0.06
Nodes (33): actions(), button(), constructor(), danger(), dispatch(), dispatchSelf(), dispatchTo(), duration() (+25 more)

### Community 67 - "_update"
Cohesion: 0.06
Nodes (56): afterBuildTicks(), afterCalculateLabelRotation(), afterDataLimits(), afterDatasetsUpdate(), afterFit(), afterSetDimensions(), afterTickToLabelConversion(), afterUpdate() (+48 more)

### Community 68 - "columns/select.js"
Cohesion: 0.06
Nodes (46): A(), applyDisabledState(), b(), Bt(), createBadgeElement(), createRemoveButton(), disable(), enable() (+38 more)

### Community 69 - "addresses-page"
Cohesion: 0.40
Nodes (4): cancel, create, delete({{ $address->id }}), edit({{ $address->id }})

### Community 70 - "composer.json (6)"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 78 - "d"
Cohesion: 0.14
Nodes (43): g(), G(), b(), $c(), X(), ca(), D(), _e() (+35 more)

### Community 79 - "product-purchase"
Cohesion: 0.50
Nodes (3): addToCart, decrement, increment

### Community 80 - "split"
Cohesion: 0.07
Nodes (54): A1(), Aa(), addAttributes(), Am(), bm(), Cr(), Dm(), m() (+46 more)

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

### Community 96 - "echo.js"
Cohesion: 0.07
Nodes (37): ar(), b(), Be(), cr(), d(), di(), e(), ei() (+29 more)

### Community 110 - "updateElements"
Cohesion: 0.06
Nodes (53): af(), afterDatasetsUpdate(), calculateCircumference(), _circumference(), _computeAngle(), countVisibleElements(), datasetAnimationScopeKeys(), df() (+45 more)

### Community 111 - "PageResource"
Cohesion: 0.16
Nodes (3): {closure#2}(), PageResource, EditPage

### Community 150 - "Order"
Cohesion: 0.06
Nodes (14): CancelUnpaidOrdersCommand, SecurityCheckCommand, PaymentException, {closure#11}(), OrderController, Controller, PaymentController, Order (+6 more)

### Community 151 - "parse"
Cohesion: 0.06
Nodes (53): defaultZone(), afterAutoSkip(), al(), buildLookupTable(), buildOrUpdateElements(), buildTicks(), Cl(), determineDataLimits() (+45 more)

### Community 153 - "tables.js"
Cohesion: 0.12
Nodes (49): ae(), B(), be(), C(), ce(), closeDropdown(), D(), de() (+41 more)

### Community 154 - "lineAt"
Cohesion: 0.06
Nodes (51): addBlock(), addElement(), addLineDeco(), applyChanges(), balanced(), baseIndent(), baseIndentFor(), blank() (+43 more)

### Community 155 - "y"
Cohesion: 0.19
Nodes (51): at(), Be(), Cr(), Ct(), me(), de(), dr(), dt() (+43 more)

### Community 156 - "I"
Cohesion: 0.06
Nodes (50): Mi(), aa(), ad(), br(), bs(), _cachedScopes(), cd(), chartOptionScopes() (+42 more)

### Community 157 - "te"
Cohesion: 0.05
Nodes (10): Ud(), Bi(), Bn(), br(), ji(), Kc(), Nd(), Ri() (+2 more)

### Community 158 - "components/select.js"
Cohesion: 0.08
Nodes (37): A(), applyDisabledState(), b(), bt(), disable(), _e(), enable(), Et() (+29 more)

### Community 159 - "r"
Cohesion: 0.05
Nodes (49): _a(), An(), bf(), bh(), Bo(), cf(), da(), l() (+41 more)

### Community 162 - "parse"
Cohesion: 0.07
Nodes (49): afterAutoSkip(), ah(), at(), buildLookupTable(), buildTicks(), cu(), determineDataLimits(), formats() (+41 more)

### Community 163 - "S"
Cohesion: 0.07
Nodes (48): aa(), an(), _calculatePadding(), _computeGridLineItems(), _computeLabelItems(), _computeLabelSizes(), computeTickLimit(), cr() (+40 more)

### Community 164 - "e"
Cohesion: 0.06
Nodes (45): apply(), bl(), bs(), calculateLabelRotation(), ci(), Cn(), Cs(), l() (+37 more)

### Community 165 - "ei"
Cohesion: 0.12
Nodes (43): ae(), an(), At(), bi(), ce(), ct(), di(), Dn() (+35 more)

### Community 166 - "init"
Cohesion: 0.09
Nodes (42): acquireScrollLock(), clearFeedback(), close(), closeMenuHierarchy(), closeQuietly(), commit(), copy(), destroy() (+34 more)

### Community 167 - "finish"
Cohesion: 0.07
Nodes (42): addChild(), addGaps(), addLeafElement(), addNode(), am(), ATXHeading(), balance(), _c() (+34 more)

### Community 168 - "De"
Cohesion: 0.12
Nodes (42): Rd(), B(), Be(), Bi(), Br(), ct(), De(), es() (+34 more)

### Community 169 - "renderOptions"
Cohesion: 0.12
Nodes (42): addBadgesForSelectedOptions(), addSingleSelectionDisplay(), closeDropdown(), commitSelectedDisplay(), constructor(), createOptionElement(), deferPositionDropdown(), destroy() (+34 more)

### Community 170 - "di"
Cohesion: 0.12
Nodes (42): ae(), At(), bi(), bn(), ce(), cn(), ct(), di() (+34 more)

### Community 171 - "n"
Cohesion: 0.09
Nodes (41): al(), An(), bd(), bo(), Bt(), co(), Dn(), En() (+33 more)

### Community 172 - "filament/app.js"
Cohesion: 0.10
Nodes (30): A(), clearTimeouts(), close(), destroy(), dismiss(), ee(), focusInitialNavigation(), focusLeft() (+22 more)

### Community 173 - "eq"
Cohesion: 0.08
Nodes (38): a$(), activeForPoint(), boundChange(), commit(), compare(), comparePoint(), compareRange(), Ds() (+30 more)

### Community 174 - "constructor"
Cohesion: 0.08
Nodes (38): Ot(), add(), bn(), bo(), _cachedScopes(), chartOptionScopes(), configure(), constructor() (+30 more)

### Community 175 - "dn"
Cohesion: 0.15
Nodes (37): aa(), Ai(), ao(), ba(), bl(), dn(), Ea(), en() (+29 more)

### Community 176 - "getContext"
Cohesion: 0.09
Nodes (36): acquireContext(), Ae(), Ca(), _computeLabelArea(), data(), Dl(), dr(), draw() (+28 more)

### Community 177 - "date-time-picker.js"
Cohesion: 0.14
Nodes (30): Ae(), Ai(), at(), Ci(), Cr(), dt(), e(), ea() (+22 more)

### Community 178 - "apply"
Cohesion: 0.09
Nodes (33): $a(), addMaps(), addStep(), addTransform(), appendMap(), appendMapping(), appendMappingInverted(), apply() (+25 more)

### Community 179 - "renderOptions"
Cohesion: 0.17
Nodes (32): closeDropdown(), constructor(), createOptionElement(), deferPositionDropdown(), filterOptions(), focusNextOption(), focusPreviousOption(), getEnabledOptions() (+24 more)

### Community 180 - "go"
Cohesion: 0.09
Nodes (32): alpha(), be(), co(), darken(), desaturate(), es(), et(), fo() (+24 more)

### Community 181 - "E"
Cohesion: 0.09
Nodes (28): addEventListener(), aspectRatio(), au(), bindResponsiveEvents(), _calculateBarIndexPixels(), E(), Eo(), fh() (+20 more)

### Community 182 - "pt"
Cohesion: 0.10
Nodes (28): active(), ai(), _animateOptions(), _createAnimations(), da(), dataset(), fa(), c() (+20 more)

### Community 183 - "A"
Cohesion: 0.10
Nodes (27): A(), ar(), aspectRatio(), Bt(), drawTitle(), ee(), fn(), fr() (+19 more)

### Community 184 - "oS"
Cohesion: 0.18
Nodes (21): attrs(), cc(), Eb(), Mo(), rc(), sc(), Yi(), Gi() (+13 more)

### Community 185 - "constructor"
Cohesion: 0.11
Nodes (21): bg(), constructor(), Cs(), eras(), fg(), ge(), getDevicePixelRatio(), getMeta() (+13 more)

### Community 186 - "_notify"
Cohesion: 0.12
Nodes (20): br(), cancel(), _createDescriptors(), _descriptors(), getPlugin(), invalidate(), ji(), kr() (+12 more)

### Community 187 - "Y"
Cohesion: 0.16
Nodes (19): B(), E(), ie(), L(), N(), T(), X(), Y() (+11 more)

### Community 188 - "va"
Cohesion: 0.16
Nodes (19): Aa(), da(), fa(), Gr(), jc(), Kr(), Ln(), ma() (+11 more)

### Community 189 - "_notify"
Cohesion: 0.14
Nodes (19): active(), _animateOptions(), cancel(), _createAnimations(), _createDescriptors(), _descriptors(), Fd(), nd() (+11 more)

### Community 190 - "updateSelectedDisplay"
Cohesion: 0.16
Nodes (18): addBadgesForSelectedOptions(), addSingleSelectionDisplay(), commitSelectedDisplay(), createBadgeElement(), createRemoveButton(), destroy(), destroyBadgesSortable(), F() (+10 more)

### Community 191 - "gu"
Cohesion: 0.16
Nodes (17): themeClasses(), Bn(), bu(), Du(), fe(), fu(), gu(), hu() (+9 more)

### Community 192 - "color-picker.js"
Cohesion: 0.13
Nodes (3): [R](), style(), update()

### Community 193 - "fn"
Cohesion: 0.22
Nodes (17): dn(), ei(), fn(), ft(), Hn(), Ht(), Ie(), ii() (+9 more)

### Community 194 - "px"
Cohesion: 0.19
Nodes (15): bx(), fr(), hx(), inline(), mx(), nh(), Oa(), Ou() (+7 more)

### Community 195 - "addEventListener"
Cohesion: 0.19
Nodes (15): Hn(), addEventListener(), bindResponsiveEvents(), isAttached(), ja(), jn(), ke(), Re() (+7 more)

### Community 196 - "toString"
Cohesion: 0.20
Nodes (14): cd(), endIndex(), getObj(), hasProtocol(), render(), startIndex(), toFormattedHref(), toFormattedObject() (+6 more)

### Community 197 - "fn"
Cohesion: 0.33
Nodes (13): fn(), hn(), Ie(), ii(), pt(), Re(), si(), Te() (+5 more)

### Community 198 - "_r"
Cohesion: 0.21
Nodes (12): Dn(), first(), pathSegment(), pluginScopeKeys(), pn(), _r(), rl(), Si() (+4 more)

### Community 199 - "jh"
Cohesion: 0.24
Nodes (11): dk(), Fh(), fk(), hk(), In(), isEmpty(), jh(), pk() (+3 more)

### Community 200 - "actions/actions.js"
Cohesion: 0.44
Nodes (8): closeModal(), generateModalId(), getActionNestingIndexFromModalId(), init(), openModal(), rememberPreviouslyFocusedElement(), restorePreviouslyFocusedElement(), syncActionModals()

### Community 201 - "Fe"
Cohesion: 0.20
Nodes (10): Ce(), De(), Dt(), Fe(), He(), ir(), Mt(), nr() (+2 more)

### Community 203 - "builder.js"
Cohesion: 0.47
Nodes (3): C(), F(), O()

### Community 204 - "شاخهٔ vercel-demo (فقط برای نمایش)"
Cohesion: 0.33
Nodes (5): main(), به‌روزرسانی دمو با آخرین تغییرات main, تفاوت‌ها با main, شاخهٔ vercel-demo (فقط برای نمایش), محدودیت‌ها

### Community 206 - "f"
Cohesion: 0.40
Nodes (4): f(), h(), c(), u()

### Community 208 - "vercel.json"
Cohesion: 0.50
Nodes (3): builds, routes, version

## Ambiguous Edges - Review These
- `Brand Sheet Preview (preview.html)` → `Self-hosted Fonts (not Google Fonts)`  [AMBIGUOUS]
  brand/preview.html · relation: conceptually_related_to

## Knowledge Gaps
- **40 isolated node(s):** `PendingPayment`, `Paid`, `Processing`, `Shipped`, `Delivered` (+35 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 1156 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **92 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `z()` connect `d` to `markdown-editor.js`, `sr`, `components/chart.js`, `filament/app.js`, `D`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Are the 18 inferred relationships involving `update()` (e.g. with `r()` and `ia()`) actually correct?**
  _`update()` has 18 INFERRED edges - model-reasoned connections that need verification._
- **What connects `PendingPayment`, `Paid`, `Processing` to the rest of the system?**
  _40 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `ProductVariant` be split into smaller, more focused modules?**
  _Cohesion score 0.09950248756218906 - nodes in this community are weakly interconnected._
- **Why does `Vm()` connect `fromObject` to `components/chart.js`, `code-editor.js`, `file-upload.js`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `constructor()` (e.g. with `Ji()` and `Ki()`) actually correct?**
  _`constructor()` has 6 INFERRED edges - model-reasoned connections that need verification._

### Low-confidence Hints
_AMBIGUOUS edges — the extractor was unsure. Verify before acting on these._

- **What is the exact relationship between `Brand Sheet Preview (preview.html)` and `Self-hosted Fonts (not Google Fonts)`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._