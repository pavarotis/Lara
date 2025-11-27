<x-public-layout>
    <x-slot name="title">About Us - {{ config('app.name') }}</x-slot>

    <section class="py-16 bg-surface">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">About Us</h1>
                <p class="text-lg text-gray-600">Learn more about our story and mission.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-8 prose prose-lg max-w-none">
                <p>
                    Welcome to <strong>{{ config('app.name', 'LaraShop') }}</strong>! We are passionate about 
                    delivering quality products and exceptional service to our customers.
                </p>
                
                <h2>Our Story</h2>
                <p>
                    Founded with a vision to make online ordering simple and enjoyable, we've grown to become 
                    a trusted name in our community. Our commitment to quality and customer satisfaction 
                    drives everything we do.
                </p>

                <h2>Our Mission</h2>
                <p>
                    To provide fresh, high-quality products with convenient ordering options, whether you 
                    prefer pickup or delivery. We believe great food should be accessible to everyone.
                </p>

                <h2>Why Choose Us?</h2>
                <ul>
                    <li>Fresh ingredients, prepared with care</li>
                    <li>Fast and reliable service</li>
                    <li>Easy online ordering</li>
                    <li>Friendly customer support</li>
                </ul>
            </div>
        </div>
    </section>
</x-public-layout>

