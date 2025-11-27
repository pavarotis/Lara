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

                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Send us a Message</h2>
                    
                    <!-- Success Message -->
                    <div id="contact-success" class="hidden mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Thank you! Your message has been sent successfully.
                        </div>
                    </div>

                    <form id="contact-form" class="space-y-4">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" name="name" id="contact_name" required
                                   class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                   placeholder="Your name">
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="contact_email" required
                                   class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                   placeholder="your@email.com">
                        </div>

                        <div>
                            <label for="contact_subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" name="subject" id="contact_subject"
                                   class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                   placeholder="How can we help?">
                        </div>

                        <div>
                            <label for="contact_message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea name="message" id="contact_message" rows="4" required
                                      class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                      placeholder="Your message..."></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-600 transition-colors flex items-center justify-center gap-2"
                                id="contact-submit">
                            <span>Send Message</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @push('scripts')
    <script>
        document.getElementById('contact-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('contact-submit');
            const successMsg = document.getElementById('contact-success');
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Sending...</span>';
            
            // Simulate form submission (replace with actual endpoint later)
            setTimeout(() => {
                form.reset();
                successMsg.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Send Message</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>';
                
                // Hide success message after 5s
                setTimeout(() => {
                    successMsg.classList.add('hidden');
                }, 5000);
            }, 1000);
        });
    </script>
    @endpush
</x-public-layout>

