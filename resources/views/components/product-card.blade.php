@props(['product'])

<div class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col">
    <!-- Product Image -->
    <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-50 relative overflow-hidden">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" 
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        <!-- Featured Badge -->
        @if($product->is_featured)
            <div class="absolute top-2 left-2 bg-accent text-white text-xs font-medium px-2 py-1 rounded-full">
                Featured
            </div>
        @endif

        <!-- Unavailable Overlay -->
        @if(!$product->is_available)
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                <span class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm font-medium">Unavailable</span>
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="p-4 flex-1 flex flex-col">
        <h3 class="font-semibold text-gray-900 group-hover:text-primary transition-colors">
            {{ $product->name }}
        </h3>
        
        @if($product->description)
            <p class="mt-1 text-gray-500 text-sm line-clamp-2 flex-1">{{ $product->description }}</p>
        @endif

        <div class="mt-3 flex items-center justify-between">
            <span class="text-lg font-bold text-primary">€{{ number_format($product->price, 2) }}</span>
            
            @if($product->is_available)
                <button type="button" 
                        class="add-to-cart-btn p-2 bg-primary-100 text-primary rounded-lg hover:bg-primary hover:text-white transition-colors"
                        data-product-id="{{ $product->id }}"
                        title="Add to cart">
                    <svg class="w-5 h-5 add-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <svg class="w-5 h-5 check-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const button = this;
            const addIcon = button.querySelector('.add-icon');
            const checkIcon = button.querySelector('.check-icon');
            
            // Disable button during request
            button.disabled = true;
            
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) cartCount.textContent = data.cart_count;
                    
                    // Show success feedback
                    addIcon.classList.add('hidden');
                    checkIcon.classList.remove('hidden');
                    button.classList.add('bg-green-500', 'text-white');
                    button.classList.remove('bg-primary-100', 'text-primary');
                    
                    // Reset after 1.5s
                    setTimeout(() => {
                        addIcon.classList.remove('hidden');
                        checkIcon.classList.add('hidden');
                        button.classList.remove('bg-green-500', 'text-white');
                        button.classList.add('bg-primary-100', 'text-primary');
                        button.disabled = false;
                    }, 1500);
                }
            })
            .catch(() => {
                button.disabled = false;
            });
        });
    });
});
</script>
@endpush
@endonce

