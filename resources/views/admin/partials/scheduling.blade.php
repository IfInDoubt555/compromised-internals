@props([
  'model' => null,
  'namePrefix' => '',
  'field' => 'status',
  'dateField' => 'published_at',
])

@php
  $current = old($namePrefix.$field, $model->{$field} ?? 'draft');

  $publishAtLocal = old($namePrefix.$dateField);
  if (!$publishAtLocal && ($model?->{$dateField} ?? null)) {
      $publishAtLocal = $model->{$dateField}
          ? $model->{$dateField}->timezone(config('app.timezone'))->format('Y-m-d\TH:i')
          : null;
  }

  $nowLocal = now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i');
@endphp

<div x-data="{ status: @js($current), nowLocal: @js($nowLocal) }" class="ci-card p-4 space-y-3">
  <label class="ci-label">Publish Status</label>

  <div class="flex flex-wrap gap-6">
    <label class="inline-flex items-center gap-2">
      <input type="radio" name="{{ $namePrefix.$field }}" value="draft" x-model="status">
      <span>Draft</span>
    </label>

    <label class="inline-flex items-center gap-2">
      <input type="radio" name="{{ $namePrefix.$field }}" value="scheduled" x-model="status">
      <span>Schedule</span>
    </label>

    <label class="inline-flex items-center gap-2">
      <input type="radio" name="{{ $namePrefix.$field }}" value="published" x-model="status">
      <span>Publish now</span>
    </label>
  </div>

  {{-- Only render the picker in the DOM when scheduling (avoids “not focusable” validation) --}}
  <template x-if="status === 'scheduled'">
    <div class="mt-2 space-y-1">
      <label for="{{ $namePrefix.$dateField }}" class="ci-label mb-1">
        Publish at (your local time)
      </label>
      <input
        id="{{ $namePrefix.$dateField }}"
        type="datetime-local"
        name="{{ $namePrefix.$dateField }}"
        class="ci-input w-64"
        value="{{ $publishAtLocal }}"
        :min="nowLocal"
        required
      >
      <p class="ci-help mt-1">Saved in UTC ({{ config('app.timezone') }} → UTC).</p>
    </div>
  </template>

  {{-- When NOT scheduling, submit an empty published_at to clear any previous schedule --}}
  <template x-if="status !== 'scheduled'">
    <input type="hidden" name="{{ $namePrefix.$dateField }}" value="">
  </template>

  @if (!empty($model?->{$dateField}))
    <p class="ci-help">
      Currently set publish time:
      {{ $model->{$dateField}->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
      ({{ config('app.timezone') }})
    </p>
  @endif
</div>