<x-public-layout>
    <x-slot name="title">Contact Us - {{ config('app.name') }}</x-slot>

    <section class="py-16 bg-surface">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h1>
                <p class="text-lg text-gray-600">We'd love to hear from you!</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Contact Info -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Get in Touch</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Address</h3>
                                <p class="text-gray-600">123 Main Street<br>Athens, Greece 10000</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Phone</h3>
                                <p class="text-gray-600">+30 210 1234567</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Email</h3>
                                <p class="text-gray-600">info@larashop.test</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t">
                        <h3 class="font-medium text-gray-900 mb-2">Hours</h3>
                        <p class="text-gray-600">Monday - Friday: 8:00 - 22:00</p>
                        <p class="text-gray-600">Saturday - Sunday: 9:00 - 23:00</p>
                    </div>
                </div>

                <!-- Contact Form Placeholder -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Send us a Message</h2>
                    
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>Contact form coming soon!</p>
                        <p class="text-sm mt-2">For now, please reach us by phone or email.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>

