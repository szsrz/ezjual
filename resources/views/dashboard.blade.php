<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div>
            <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading>{{ __('Ringkasan sistem jualan anda') }}</flux:subheading>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/15 dark:stroke-neutral-100/15" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/15 dark:stroke-neutral-100/15" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/15 dark:stroke-neutral-100/15" />
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/15 dark:stroke-neutral-100/15" />
        </div>
    </div>
</x-layouts::app>
