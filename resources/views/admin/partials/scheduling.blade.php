@props(['model','namePrefix' => '', 'field' => 'status'])

@php
  $current = old($namePrefix.$field, $model->{$field} ?? 'draft');
  $scheduledFor = old($namePrefix.'scheduled_for', optional($model->scheduled_for)->format('Y-m-d\TH:i'));
@endphp

<div x-data="{ status: '{{ $current }}' }" class="ci-card p-4">
  <label class="block text-sm font-semibold mb-2">Publish Status</label>
  <div class="flex flex-wrap gap-4">
    <label class="inline-flex items-center gap-2">
      <input type="radio" name="{{ $namePrefix.$field }}" value="draft" x-model="status">
      <span>Draft</span>
    </label>
    <label class="inline-flex items-center gap-2">
      <input type="radio" name="{{ $namePrefix.$field }}" value="scheduled" x-model="status">
      <span>Scheduled</span>
    </label>
    <label class="inline-flex items-center gap-2">
      <input type="radio" name="{{ $namePrefix.$field }}" value="published" x-model="status">
      <span>Publish now</span>
    </label>
  </div>

  <div class="mt-3" x-show="status === 'scheduled'">
    <label class="block text-sm font-semibold mb-1">Publish at (your local time)</label>
    <input type="datetime-local" name="{{ $namePrefix }}scheduled_for"
           value="{{ $scheduledFor }}" class="w-full border rounded px-3 py-2">
    <p class="text-xs ci-muted mt-1">Stored in UTC.</p>
  </div>

  @if(!empty($model->published_at))
    <p class="text-xs mt-2">
      Published at: {{ $model->published_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i') }}
    </p>
  @endif
</div>