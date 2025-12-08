# 🔌 v2 Plugin Development Guide

## Overview

Plugin system για επέκταση του CMS. Plugins μπορούν να προσθέτουν blocks, menu items, API endpoints, και custom functionality.

**Related Documentation:**
- [v2 Overview](./v2_overview.md) — Architecture, strategy & technical specs
- [v2 Content Model](./v2_content_model.md) — Block structure

---

## 📁 Plugin Structure

```
plugins/
└── example-plugin/
    ├── src/
    │   ├── ExamplePluginServiceProvider.php
    │   ├── Blocks/
    │   │   └── TestimonialBlock.php
    │   ├── Controllers/
    │   │   └── ExampleController.php
    │   └── Routes/
    │       └── web.php
    ├── resources/
    │   └── views/
    │       └── blocks/
    │           └── testimonial.blade.php
    ├── database/
    │   └── migrations/
    │       └── 2024_01_01_create_testimonials_table.php
    └── plugin.json
```

---

## 📄 plugin.json

```json
{
  "name": "example-plugin",
  "title": "Example Plugin",
  "version": "1.0.0",
  "description": "Adds testimonial blocks",
  "author": "Your Name",
  "license": "MIT",
  "requires": {
    "larashop": ">=2.0.0"
  }
}
```

---

## 🔧 Service Provider

```php
<?php

namespace Plugins\ExamplePlugin;

use Illuminate\Support\ServiceProvider;
use App\Domain\Content\Services\BlockRegistry;

class ExamplePluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register plugin
    }

    public function boot(): void
    {
        // Register blocks
        BlockRegistry::register('testimonial', TestimonialBlock::class);
        
        // Register routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        
        // Register views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'example-plugin');
        
        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
```

---

## 🧩 Custom Blocks

### Block Class

```php
<?php

namespace Plugins\ExamplePlugin\Blocks;

use App\Domain\Content\Contracts\BlockInterface;

class TestimonialBlock implements BlockInterface
{
    public function getType(): string
    {
        return 'testimonial';
    }

    public function getConfigSchema(): array
    {
        return [
            'name' => 'text',
            'role' => 'text',
            'content' => 'textarea',
            'avatar' => 'media',
        ];
    }

    public function render(array $props): string
    {
        return view('example-plugin::blocks.testimonial', $props)->render();
    }
}
```

### Block View

```blade
{{-- resources/views/blocks/testimonial.blade.php --}}
<div class="testimonial">
    <img src="{{ $props['avatar'] }}" alt="{{ $props['name'] }}">
    <h3>{{ $props['name'] }}</h3>
    <p class="role">{{ $props['role'] }}</p>
    <p class="content">{{ $props['content'] }}</p>
</div>
```

### Admin Block Config Form

```blade
{{-- resources/views/admin/blocks/testimonial-config.blade.php --}}
<div class="block-config testimonial">
    <label>Name</label>
    <input type="text" name="name" value="{{ $props['name'] ?? '' }}">
    
    <label>Role</label>
    <input type="text" name="role" value="{{ $props['role'] ?? '' }}">
    
    <label>Content</label>
    <textarea name="content">{{ $props['content'] ?? '' }}</textarea>
    
    <label>Avatar</label>
    <x-admin-media-picker name="avatar" :value="$props['avatar'] ?? null" />
</div>
```

---

## 🎣 Plugin Hooks

### Available Hooks

```php
// Register menu item
Hook::register('admin.menu', function ($menu) {
    $menu->add('Example', '/admin/example', 'icon');
});

// Register API route
Hook::register('api.routes', function ($router) {
    $router->get('/example', [ExampleController::class, 'index']);
});

// Register content block
Hook::register('content.blocks', function ($registry) {
    $registry->register('testimonial', TestimonialBlock::class);
});

// Register widget
Hook::register('dashboard.widgets', function ($widgets) {
    $widgets->add('example-widget', ExampleWidget::class);
});
```

---

## 📦 Plugin Installation

### Manual Installation

1. Copy plugin to `plugins/example-plugin/`
2. Register in `config/plugins.php`:
```php
return [
    'plugins' => [
        \Plugins\ExamplePlugin\ExamplePluginServiceProvider::class,
    ],
];
```

3. Run migrations:
```bash
php artisan migrate
```

### Composer Package (Future)

```json
{
  "name": "your-org/example-plugin",
  "type": "larashop-plugin",
  "extra": {
    "larashop-plugin": {
      "provider": "Plugins\\ExamplePlugin\\ExamplePluginServiceProvider"
    }
  }
}
```

---

## 🧪 Testing Plugins

```php
<?php

namespace Tests\Plugins\ExamplePlugin;

use Tests\TestCase;
use Plugins\ExamplePlugin\Blocks\TestimonialBlock;

class TestimonialBlockTest extends TestCase
{
    public function test_block_renders(): void
    {
        $block = new TestimonialBlock();
        $props = [
            'name' => 'John Doe',
            'role' => 'Developer',
            'content' => 'Great plugin!',
        ];
        
        $html = $block->render($props);
        
        $this->assertStringContainsString('John Doe', $html);
    }
}
```

---

## 📚 Best Practices

1. **Namespace**: Use `Plugins\{PluginName}\`
2. **Dependencies**: Declare in `plugin.json`
3. **Migrations**: Prefix with plugin name
4. **Views**: Use plugin namespace (`plugin-name::view`)
5. **Assets**: Publish to public (if needed)
6. **Config**: Use plugin config file
7. **Tests**: Write tests for your plugin

---

## 🚀 Example: Complete Plugin

See `plugins/example/` for a complete working example.

---

**End of Plugin Guide**

---

**Last Updated**: 2024-11-27

