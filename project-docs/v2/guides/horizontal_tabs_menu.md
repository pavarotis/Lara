# Οριζόντιο Menu (Tabs) στο Admin Panel

**Last Updated**: 2025-01-20  
**Purpose**: Οδηγός για δημιουργία οριζόντιου menu με Tabs σε Filament Pages

---

## 📋 Overview

Όταν ένα Filament Page έχει **πολλές κατηγορίες/λειτουργίες** που πρέπει να οργανωθούν, χρησιμοποιούμε **οριζόντιο menu με Tabs** αντί για πολλά sections ή scrollable content.

**Χρήση:**
- ✅ Pages με 3+ κατηγορίες/λειτουργίες
- ✅ Όταν θέλουμε να οργανώσουμε διαφορετικούς τύπους περιεχομένου (forms, tables, previews)
- ✅ Όταν θέλουμε να μειώσουμε το scroll και να βελτιώσουμε το UX

**Παράδειγμα:** Complete SEO page με Global SEO, Sitemap, JSON-LD, Robots.txt, URL Redirection

---

## 🔧 Implementation

### 1. Προσθήκη Livewire Property

Στο Page class, προσθέτουμε property για το active tab:

```php
<?php

namespace App\Filament\Pages\Extensions;

use Filament\Pages\Page;

class CompleteSEO extends Page
{
    public string $activeTab = 'global'; // Default tab
    
    // ... rest of the class
}
```

### 2. Blade View Structure

Στο Blade view, χρησιμοποιούμε `<x-filament::tabs>` component:

```blade
<x-filament-panels::page>
    <!-- Tabs Menu -->
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$activeTab === 'global'"
            wire:click="$set('activeTab', 'global')"
        >
            Global SEO
        </x-filament::tabs.item>
        
        <x-filament::tabs.item
            :active="$activeTab === 'sitemap'"
            wire:click="$set('activeTab', 'sitemap')"
        >
            Sitemap
        </x-filament::tabs.item>
        
        <!-- Add more tabs as needed -->
    </x-filament::tabs>

    <!-- Tab Content -->
    <div class="mt-6">
        @if($activeTab === 'global')
            <!-- Content for Global SEO tab -->
            <form wire:submit="save">
                {{ $this->form }}
                <!-- ... -->
            </form>
        @elseif($activeTab === 'sitemap')
            <!-- Content for Sitemap tab -->
            <x-filament::section>
                <!-- ... -->
            </x-filament::section>
        @elseif($activeTab === 'jsonld')
            <!-- Content for JSON-LD tab -->
            <!-- ... -->
        @endif
    </div>
</x-filament-panels::page>
```

---

## 📝 Key Points

### ✅ DO's

1. **Label ως Content**: Το label του tab πρέπει να είναι **content** μέσα στο `<x-filament::tabs.item>`, όχι attribute:
   ```blade
   <!-- ✅ CORRECT -->
   <x-filament::tabs.item :active="...">
       Global SEO
   </x-filament::tabs.item>
   
   <!-- ❌ WRONG -->
   <x-filament::tabs.item label="Global SEO" />
   ```

2. **Default Tab**: Πάντα ορίζουμε default tab στο property:
   ```php
   public string $activeTab = 'first_tab';
   ```

3. **Wire Click**: Χρησιμοποιούμε `wire:click="$set('activeTab', 'tab_name')"` για tab switching

4. **Conditional Content**: Χρησιμοποιούμε `@if/@elseif` για να εμφανίζουμε το σωστό περιεχόμενο

### ❌ DON'Ts

1. **Μην χρησιμοποιείς `label` attribute** - δεν λειτουργεί
2. **Μην βάζεις content μέσα στα tabs.items** - τα items είναι μόνο για navigation
3. **Μην ξεχνάς το `mt-6` spacing** μεταξύ tabs και content

---

## 🎨 Styling

Τα tabs έχουν default Filament styling. Αν χρειάζεται customization:

```blade
<x-filament::tabs class="custom-tabs-class">
    <!-- tabs -->
</x-filament::tabs>
```

---

## 📚 Examples

### Example 1: Simple Tabs (2-3 tabs)

```blade
<x-filament::tabs>
    <x-filament::tabs.item :active="$activeTab === 'settings'" wire:click="$set('activeTab', 'settings')">
        Settings
    </x-filament::tabs.item>
    <x-filament::tabs.item :active="$activeTab === 'preview'" wire:click="$set('activeTab', 'preview')">
        Preview
    </x-filament::tabs.item>
</x-filament::tabs>
```

### Example 2: Tabs with Icons (Future Enhancement)

```blade
<x-filament::tabs.item 
    :active="$activeTab === 'global'"
    wire:click="$set('activeTab', 'global')"
    icon="heroicon-o-magnifying-glass"
>
    Global SEO
</x-filament::tabs.item>
```

---

## 🔄 Alternative: Schema Tabs (for Forms only)

Αν έχεις **μόνο form fields** (όχι mixed content), μπορείς να χρησιμοποιήσεις Tabs στο Schema:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

public function form(Schema $schema): Schema
{
    return $schema
        ->components([
            Tabs::make('MainTabs')
                ->tabs([
                    Tab::make('General')
                        ->schema([
                            TextInput::make('name'),
                            // ...
                        ]),
                    Tab::make('SEO')
                        ->schema([
                            TextInput::make('meta_title'),
                            // ...
                        ]),
                ]),
        ]);
}
```

**Χρήση:**
- ✅ Μόνο για forms (όλα τα fields είναι στο form)
- ❌ Δεν λειτουργεί για mixed content (forms + tables + previews)

---

## ✅ Checklist

Πριν προσθέσεις tabs σε ένα Page:

- [ ] Έχω 3+ κατηγορίες/λειτουργίες που χρειάζονται οργάνωση
- [ ] Προσθέτω `public string $activeTab = 'default';` στο Page class
- [ ] Χρησιμοποιώ `<x-filament::tabs>` στο Blade view
- [ ] Το label είναι content, όχι attribute
- [ ] Χρησιμοποιώ `wire:click="$set('activeTab', 'name')"` για switching
- [ ] Προσθέτω `@if/@elseif` για conditional content
- [ ] Προσθέτω `mt-6` spacing μεταξύ tabs και content

---

## 📖 Related Documentation

- [Filament 4 API Reference](./filament_4_api_reference.md)
- [Hybrid Admin Panel Guidelines](../architecture/hybrid_patterns.md)

---

## 🔗 Reference Implementation

**File**: `app/Filament/Pages/Extensions/CompleteSEO.php`  
**View**: `resources/views/filament/pages/extensions/complete-seo.blade.php`

**Tabs:**
- Global SEO (form)
- Sitemap (preview)
- JSON-LD (preview)
- Robots.txt (preview)
- URL Redirection (table)
