@props(['model','namePrefix' => '', 'field' => 'status'])

@php
  // Current status value (defaults to draft)
  $current = old($namePrefix.$field, $model->{$field} ?? 'draft');

  // Localized datetime-local value for scheduling (uses published_at as the canonical time)
  $publishAtLocal = old(
      $namePrefix.'published_at',
      optional($model->published_at)
          ? $model->published_at->timezone(config('app.timezone'))->format('Y-m-d\TH:i')
          : null
  );

  // For convenience, "now" in app timezone (optional min attr)
  $nowLocal = now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i');
@endphp

<div x-data="{ status: @js($current) }" class="ci-card p-4 space-y-3">
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
      {{-- keep value "published" to match controller validation --}}
      <input type="radio" name="{{ $namePrefix.$field }}" value="published" x-model="status">
      <span>Publish now</span>
    </label>
  </div>

  {{-- Schedule picker (published_at in local time) --}}
  <div class="mt-2 space-y-1" x-show="status === 'scheduled'" x-cloak>
    <label for="{{ $namePrefix }}published_at" class="ci-label mb-1">Publish at (your local time)</label>
    <input
      id="{{ $namePrefix }}published_at"
      type="datetime-local"
      name="{{ $namePrefix }}published_at"
      class="ci-input"
      value="{{ $publishAtLocal }}"
      min="{{ $nowLocal }}"
      x-bind:required="status === 'scheduled'"
    >
    <p class="ci-help mt-1">This will be saved in UTC ({{ config('app.timezone') }} → UTC).</p>
  </div>

  @if(!empty($model->published_at))
    <p class="ci-help">
      Currently set publish time:
      {{ $model->published_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
      ({{ config('app.timezone') }})
    </p>
  @endif
</div>