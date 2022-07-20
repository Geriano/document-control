<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Document/Index');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1000',
            'order.key' => 'nullable|string',
            'order.dir' => 'nullable|in:asc,desc',
        ]);

        return Document::where(function (Builder $query) use ($request) {
            $model = new Document();
            $search = '%' . $request->search . '%';

            foreach ($model->getFillable() as $column) {
                $query->orWhere($column, 'like', $search);
            }
        })
        ->orderBy($request->input('order.key') ?: 'name', $request->input('order.dir') ?: 'asc')
        ->paginate($request->per_page ?: 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Document/Create')->with([
            'users' => User::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:documents',
            'max_revision_interval' => 'required|integer',
        ]);

        if ($document = Document::create($request->all())) {
            return redirect()->back()->with('success', __(
                'document `:name` has been created', [
                    'name' => $document->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t create document',
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        return Inertia::render('Document/Show')->with([
            'document' => $document,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        return Inertia::render('Document/Edit')->with([
            'document' => $document,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string', Rule::unique('documents')->ignore($document->id)],
            'max_revision_interval' => ['required', 'integer'],
        ]);

        if ($document->update($request->all())) {
            return redirect()->back()->with('success', __(
                'document `:name` has been updated', [
                    'name' => $document->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t update document',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        if ($document->delete()) {
            return redirect()->back()->with('success', __(
                'document `:name` has been deleted', [
                    'name' => $document->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t delete document',
        ));
    }

    /**
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\Response
     */
    public function revisions(Document $document)
    {
        return Inertia::render('Document/Revision')->with([
            'document' => $document,
        ]);
    }
}