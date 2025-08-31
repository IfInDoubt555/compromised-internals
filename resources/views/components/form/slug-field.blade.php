{{-- resources/views/components/form/slug-field.blade.php --}}

{{-- Post Tags (keeps name="slug_mode" => 'auto' | 'manual' for compatibility) --}}
<div x-data="tagBox({
        initial: @json(old('tags', isset($tags) ? $tags : [])),
        mode: '{{ old('slug_mode', $defaultMode ?? 'auto') }}'
     })" class="space-y-3 mb-6">

    {{-- Mode --}}
    <div>
        <label class="block text-sm font-medium mb-1 text-gray-900 dark:text-stone-100">Post Tags</label>
        <div class="flex gap-6 text-sm">
            <label class="inline-flex items-center gap-2 cursor-pointer text-gray-700 dark:text-stone-300">
                <input type="radio" name="slug_mode" value="auto" x-model="mode"
                       class="size-4 text-sky-600 dark:text-emerald-500 focus:ring-sky-400 dark:focus:ring-emerald-400">
                Automatic
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer text-gray-700 dark:text-stone-300">
                <input type="radio" name="slug_mode" value="manual" x-model="mode"
                       class="size-4 text-sky-600 dark:text-emerald-500 focus:ring-sky-400 dark:focus:ring-emerald-400">
                User Defined
            </label>
        </div>
    </div>

    {{-- Tokenized input --}}
    <div>
        <label class="block text-sm font-medium mb-1 text-gray-900 dark:text-stone-100">Tags</label>

        <div
          :class="mode === 'manual' ? 'opacity-100' : 'opacity-60 pointer-events-none select-none'"
          class="w-full rounded-xl border px-3 py-2
                 bg-white text-gray-900 border-gray-300
                 dark:bg-stone-800/70 dark:text-stone-100 dark:border-white/10">

            <div class="flex flex-wrap gap-2">
                {{-- pills --}}
                <template x-for="(t,i) in tags" :key="t">
                    <span
                      class="inline-flex items-center gap-1 rounded-lg border px-2 py-1 text-xs
                             bg-gray-100 border-gray-300 text-gray-800
                             dark:bg-stone-700 dark:border-stone-600 dark:text-stone-100">
                        <span x-text="t"></span>
                        <button type="button" @click="remove(i)"
                                class="opacity-70 hover:opacity-100 focus:outline-none"
                                aria-label="Remove tag">&times;</button>
                    </span>
                </template>

                {{-- input --}}
                <input x-ref="input"
                       x-model="draft"
                       @keydown.enter.prevent="commit()"
                       @keydown.,.prevent="commit()"
                       @keydown.backspace="maybeBackspace($event)"
                       @paste.prevent="onPaste($event)"
                       :disabled="mode !== 'manual'"
                       placeholder="Use lowercase letters and dashes (e.g. rally-winter-blast)"
                       class="flex-1 min-w-[12rem] bg-transparent outline-none placeholder-gray-400
                              dark:placeholder-stone-500">
            </div>
        </div>

        {{-- payloads: array + joined string for legacy --}}
        <template x-for="t in tags" :key="t">
            <input type="hidden" name="tags[]" :value="t">
        </template>
        <input type="hidden" name="tags" :value="tags.join(',')">

        <p class="text-xs mt-1 text-gray-500 dark:text-stone-400">
            Press <span class="font-semibold">Enter</span> or <span class="font-semibold">,</span> to add. Auto-slug to lowercase-kebab.
        </p>
    </div>
</div>

@pushOnce('scripts')
<script>
  function tagBox({ initial = [], mode = 'auto' } = {}) {
    return {
      tags: Array.isArray(initial) ? initial.map(sanitize) :
            String(initial || '').split(',').map(sanitize).filter(Boolean),
      draft: '',
      mode,

      commit() {
        const v = sanitize(this.draft);
        if (v && !this.tags.includes(v)) this.tags.push(v);
        this.draft = '';
      },
      remove(i) { this.tags.splice(i, 1); },
      maybeBackspace(e) {
        if (this.draft === '' && this.tags.length && e.target.selectionStart === 0) {
          this.tags.pop();
          e.preventDefault();
        }
      },
      onPaste(e) {
        const text = (e.clipboardData || window.clipboardData).getData('text');
        text.split(/[,\n]+/).map(sanitize).forEach(v => {
          if (v && !this.tags.includes(v)) this.tags.push(v);
        });
      },
    };

    function sanitize(s) {
      return String(s || '')
        .toLowerCase().trim()
        .replace(/[^a-z0-9\-_\s]/g, '-')  // remove unsupported chars
        .replace(/[\s_]+/g, '-')          // spaces/underscores -> dash
        .replace(/-+/g, '-')              // collapse dashes
        .replace(/^-|-$/g, '');           // trim dashes
    }
  }
</script>
@endpushOnce