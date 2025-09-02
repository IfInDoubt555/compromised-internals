@props([
  'model' => null,
  'namePrefix' => '',
  'field' => 'status',        // radio group field name base (e.g., 'status')
  'dateField' => 'published_at', // <-- NEW: datetime field base name ('published_at' or 'scheduled_for')
])

@php
  // Current status (fallback to draft)
  $current = old($namePrefix.$field, $model->{$field} ?? 'draft');

  // Build the datetime-local value safely (works for either published_at or scheduled_for)
  $publishAtLocal = old($namePrefix.$dateField);
  if (!$publishAtLocal && ($model?->{$dateField} ?? null)) {
      $publishAtLocal = $model->{$dateField}
          ? $model->{$dateField}->timezone(config('app.timezone'))->format('Y-m-d\TH:i')
          : null;
  }

  // min=now for the picker
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

  {{-- Date/time picker (only when scheduling) --}}
  <div class="mt-2 space-y-1" x-show="status === 'scheduled'" x-cloak>
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
      x-bind:required="status === 'scheduled'">
    <p class="ci-help mt-1">Saved in UTC ({{ config('app.timezone') }} → UTC).</p>
  </div>

  @if (!empty($model?->{$dateField}))
    <p class="ci-help">
      Currently set publish time:
      {{ $model->{$dateField}->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
      ({{ config('app.timezone') }})
    </p>
  @endif
</div>