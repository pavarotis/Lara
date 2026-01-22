<div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header-actions-container flex items-center gap-4 sm:px-6" style="padding-top: 1rem; padding-bottom: 1.5rem; padding-left: 1.5rem; padding-right: 1.5rem;">
        <div class="grid gap-y-4">
            @if(isset($heading))
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white" style="margin-bottom: 0.25rem;">
                    {{ $heading }}
                </h3>
            @endif
            @if(isset($description))
                <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                    {{ $description }}
                </p>
            @endif
        </div>
    </div>
</div>
