<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttributionController extends Controller
{
    protected $csvPath = 'attribution-log.csv';

    public function index(Request $request)
    {
        $showAll = $request->query('all') === '1';

        $rows = array_map('str_getcsv', file(storage_path('app/' . $this->csvPath)));
        $headers = array_map('trim', array_shift($rows));

        $entries = collect($rows)->map(function ($row) use ($headers) {
            return array_combine($headers, $row);
        });

        if (!$showAll) {
            $entries = $entries->filter(function ($entry) {
                return empty(trim($entry['Author'])) || empty(trim($entry['Source URL'])) || empty(trim($entry['License Type']));
            });
        }

        return view('admin.attributions', [
            'entries' => $entries,
            'showAll' => $showAll,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->input('attributions');

        $headers = [
            'Filename', 'Path', 'Width', 'Height', 'Size (KB)', 'MIME Type',
            'Year (Guess)', 'Section (Guess)', 'Source URL', 'Author', 'License Type', 'Credit String'
        ];

        $updated = [$headers];

        foreach ($data as $item) {
            $credit = "Photo by {$item['Author']} via {$item['Source URL']} – {$item['License Type']}";
            $item['Credit String'] = $credit;

            $updated[] = array_map(function ($header) use ($item) {
                return $item[$header] ?? '';
            }, $headers);
        }

        $handle = fopen(storage_path('app/' . $this->csvPath), 'w');
        foreach ($updated as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return redirect()->back()->with('success', 'Attributions updated.');
    }
}
