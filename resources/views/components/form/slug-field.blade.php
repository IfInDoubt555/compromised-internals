{{-- resources/views/components/form/slug-field.blade.php --}}

<!-- Slug Mode Radio Buttons -->
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Slug Mode</label>
    <div class="flex gap-4">
        <label class="flex items-center">
            <input type="radio" name="slug_mode" value="auto"
                   {{ old('slug_mode', $defaultMode ?? 'auto') === 'auto' ? 'checked' : '' }} class="mr-2">
            Automatic
        </label>
        <label class="flex items-center">
            <input type="radio" name="slug_mode" value="manual"
                   {{ old('slug_mode', $defaultMode ?? 'auto') === 'manual' ? 'checked' : '' }} class="mr-2">
            User Defined
        </label>
    </div>
</div>

<!-- Slug Input -->
<div id="slug-input-wrapper" class="mb-4">
    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
    <input
        type="text"
        name="slug"
        id="slug"
        value="{{ old('slug', $slug ?? '') }}"
        class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 focus:outline-none transition bg-white"
    >
    <small class="text-gray-500">Use lowercase letters and dashes (e.g. rally-winter-blast)</small>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slugWrapper = document.getElementById('slug-input-wrapper');
        const radios = document.querySelectorAll('input[name="slug_mode"]');

        function toggleSlugField() {
            const selected = document.querySelector('input[name="slug_mode"]:checked')?.value;
            if (slugWrapper && selected) {
                slugWrapper.classList.toggle('hidden', selected !== 'manual');
            }
        }

        radios.forEach(radio => radio.addEventListener('change', toggleSlugField));
        toggleSlugField(); // Check current value on load
    });
</script>
@endpush
