<?php

use App\Message;

Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    Route::get('/', function () {
        return redirect('/posta/entrata');
    });
    Route::get('/posta/{type}', function ($type) {
        if (!in_array($type, ['entrata', 'uscita'])) {
            return redirect('/posta/entrata');
        }
        return view('vue')->with([
            'title' => $type,
            'vue' => 'arc-table',
            'params' => [
                'type' => $type,
            ],
        ]);
    });
    Route::get('api/messages/{type}', function ($type) {
        if (!in_array($type, ['entrata', 'uscita'])) {
            abort(400);
        }

        $f = request()->all();
        $q = Message::whereType($type)->orderBy('created_at', 'desc');

        if (@$f['year']) $q->forYear($f['year']);
        if (@$f['office']) $q->where('office', $f['office']);
        if (@$f['mean_of_arrival']) $q->where('mean_of_arrival', $f['mean_of_arrival']);
        if (@$f['location']) $q->where('location', $f['location']);
        if (@$f['sender_code']) $q->where('sender_code', $f['sender_code']);
        if (@$f['sender_name']) $q->where('sender_name', $f['sender_name']);

        return [
            'messages' => $q->get(),
            'years' => Message::whereType($type)->distinct()
                ->selectRaw('YEAR(doc_date) as y')->pluck('y')->union([date('Y')])->unique()->sort()->values(),
            'offices' => Message::whereType($type)->distinct()
                ->pluck('office')->sort()->values(),
            'means_of_arrival' => Message::whereType($type)->distinct()
                ->pluck('mean_of_arrival')->sort()->values(),
            'sender_codes' => Message::whereType($type)->distinct()
                ->pluck('sender_code')->sort()->values(),
            'sender_names' => Message::whereType($type)->distinct()
                ->pluck('sender_name')->sort()->values(),
            'locations' => Message::whereType($type)->distinct()
                ->pluck('location')->sort()->values(),
        ];
    });
    Route::get('api/form-data', function () {
        return [
            'sender_codes' => Message::distinct()->pluck('sender_code')->sort()->values(),
            'sender_names' => Message::distinct()->pluck('sender_name')->sort()->values(),
            'offices' => Message::distinct()->pluck('office')->sort()->values(),
            'means_of_arrival' => Message::distinct()->pluck('mean_of_arrival')->sort()->values(),
            'dossiers' => Message::getDossiers(),
            'locations' => Message::distinct()->pluck('location')->sort()->values(),
        ];
    });
    Route::delete('api/messages/{id}', function (int $id) {
        Message::findOrFail($id)->delete();
    });
    Route::post('api/messages/{type}', function ($type) {
        $data = [
            'type' => request('type'),
            'reg_date' => request('reg_date'),
            'doc_date' => request('doc_date'),
            'ext_pr' => request('ext_pr'),
            'sender_code' => request('sender_code'),
            'sender_name' => request('sender_name'),
            'notes' => (string) request('notes'),
            'office' => request('office'),
            'mean_of_arrival' => request('mean_of_arrival'),
            'dossier' => request('dossier'),
            'location' => request('location'),
        ];
        if ($doc = request()->file('doc')) {
            $data['file_token'] = basename($doc->store('public'));
            if (!$data['file_token']) {
                abort(400, 'Impossibile caricare il file');
            }
        }

        if (!mb_strlen($data['sender_code'])) abort(400, 'Compilare tutti i campi');
        if (!mb_strlen($data['sender_name'])) abort(400, 'Compilare tutti i campi');
        if (!mb_strlen($data['office'])) abort(400, 'Compilare tutti i campi');

        $x = null;
        if ($id = request('id')) {
            $x = Message::findOrFail($id);
            $x->update($data);
        } else {
            $data['int_pr'] = Message::whereType($data['type'])
                ->forYear($data['doc_date'])
                ->max('int_pr')+1;
            $x = Message::create($data);
        }
        return $x;
    });
});
