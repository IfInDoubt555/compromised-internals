@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Affiliate Clicks</h1>

  {{-- Stats --}}
  <div class="grid grid-cols-3 gap-4 mb-6">
    <div class="ci-card p-4"><div class="text-sm ci-muted">Today</div><div class="text-2xl font-semibold">{{ $stats['today'] }}</div></div>
    <div class="ci-card p-4"><div class="text-sm ci-muted">Last 7 days</div><div class="text-2xl font-semibold">{{ $stats['7d'] }}</div></div>
    <div class="ci-card p-4"><div class="text-sm ci-muted">Last 30 days</div><div class="text-2xl font-semibold">{{ $stats['30d'] }}</div></div>
  </div>

  {{-- Charts --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="ci-card p-4">
      <h2 class="text-lg font-semibold mb-2">Clicks Over Time</h2>
      <canvas id="clicksChart" height="120"></canvas>
    </div>
    <div class="ci-card p-4">
      <h2 class="text-lg font-semibold mb-2">Clicks by Brand</h2>
      <canvas id="brandChart" height="120"></canvas>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" class="ci-card p-4 mb-6 grid md:grid-cols-6 gap-3">
    <select name="brand" class="ci-input">
      <option value="">All brands</option>
      @foreach($brands as $b)
        <option value="{{ $b }}" @selected(request('brand')===$b)>{{ ucfirst($b) }}</option>
      @endforeach
    </select>
    <input name="subid" value="{{ request('subid') }}" placeholder="SubID" class="ci-input">
    <input name="host" value="{{ request('host') }}" placeholder="Host contains…" class="ci-input">
    <input type="date" name="from" value="{{ request('from') }}" class="ci-input">
    <input type="date" name="to"   value="{{ request('to') }}"   class="ci-input">
    <div class="flex gap-2">
      <button class="btn btn-primary">Filter</button>
      <a href="{{ route('admin.affiliates.clicks.export', request()->all()) }}" class="btn btn-secondary">Export CSV</a>
    </div>
  </form>

  {{-- Table --}}
  <div class="overflow-x-auto ci-card">
    <table class="min-w-full text-sm">
      <thead class="text-left border-b">
        <tr>
          <th class="py-2 px-3">Time</th>
          <th class="py-2 px-3">Brand</th>
          <th class="py-2 px-3">SubID</th>
          <th class="py-2 px-3">Host</th>
          <th class="py-2 px-3">URL</th>
          <th class="py-2 px-3">IP</th>
          <th class="py-2 px-3">User</th>
          <th class="py-2 px-3">Referrer</th>
        </tr>
      </thead>
      <tbody>
        @forelse($clicks as $c)
        <tr class="border-b border-gray-200 dark:border-zinc-800">
          <td class="py-2 px-3 whitespace-nowrap">{{ $c->created_at->format('Y-m-d H:i') }}</td>
          <td class="py-2 px-3">{{ $c->brand ?: '—' }}</td>
          <td class="py-2 px-3">{{ $c->subid ?: '—' }}</td>
          <td class="py-2 px-3">{{ $c->host }}</td>
          <td class="py-2 px-3 max-w-[360px] truncate">
            <a href="{{ $c->url }}" class="text-blue-600 dark:text-sky-400 hover:underline" target="_blank" rel="noopener">open</a>
          </td>
          <td class="py-2 px-3">{{ $c->ip }}</td>
          <td class="py-2 px-3">{{ $c->user_id ?: 'guest' }}</td>
          <td class="py-2 px-3 max-w-[240px] truncate" title="{{ $c->referer }}">{{ $c->referer ?: '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="py-6 px-3 text-center ci-muted">No clicks yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $clicks->links() }}</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Fetch daily clicks
  fetch("{{ route('admin.affiliates.clicks.chart') }}")
    .then(r => r.json())
    .then(data => {
      const labels = Object.keys(data);
      const values = Object.values(data);

      new Chart(document.getElementById('clicksChart'), {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Clicks',
            data: values,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.2)',
            tension: 0.2,
            fill: true
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            x: { title: { display: true, text: 'Date' }},
            y: { title: { display: true, text: 'Clicks' }, beginAtZero: true }
          }
        }
      });
    });

  // Fetch brand breakdown
  fetch("{{ route('admin.affiliates.clicks.chart') }}?group=brand")
    .then(r => r.json())
    .then(data => {
      const labels = Object.keys(data);
      const values = Object.values(data);

      new Chart(document.getElementById('brandChart'), {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    });
});
</script>
@endpush