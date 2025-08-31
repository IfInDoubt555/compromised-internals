@props(['model','namePrefix' => '', 'field' => 'status'])

@php
  $current = old($namePrefix.$field, $model->{$field} ?? 'draft');
  $scheduledFor = old($namePrefix.'scheduled_for', optional($model->scheduled_for)->format('Y-m-d\TH:i'));
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
      <span>Scheduled</span>
    </label>
    <label class="inline-flex items-center gap-2">
      {{-- keep value "published" to match controller validation; label says Publish now --}}
      <input type="radio" name="{{ $namePrefix.$field }}" value="published" x-model="status">
      <span>Publish now</span>
    </label>
  </div>

  <div class="mt-2" x-show="status === 'scheduled'" x-cloak>
    <label class="ci-label mb-1">Publish at (your local time)</label>
    <input type="datetime-local"
           name="{{ $namePrefix }}scheduled_for"
           value="{{ $scheduledFor }}"
           class="ci-input">
    <p class="ci-help mt-1">Stored in UTC.</p>
  </div>

  @if(!empty($model->published_at))
    <p class="ci-help">
      Published at:
      {{ $model->published_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i') }}
    </p>
  @endif
</div>