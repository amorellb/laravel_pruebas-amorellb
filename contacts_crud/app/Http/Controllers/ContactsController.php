<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContacts;
use App\Models\Contacts;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Contacts::class);

//        QueryBuilder
//        $query = DB::table('contacts')->where('user_id', Auth::id())->get();
//        $contacts = $query->all();
        $contacts = Contacts::where('user_id', Auth::id())->latest()->paginate(5);
        return view('contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Contacts::class);

        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreContacts $request
     * @return RedirectResponse
     */
    public function store(StoreContacts $request)
    {
        $request['slug'] = Str::slug($request->name, '-');

        $contact = new Contacts();
        $contact->name = $request->name;
        $contact->slug = $request->slug;
        $contact->birth_date = $request->birth_date;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->country = $request->country;
        $contact->address = $request->address;
        $contact->job_contact = $request->job_contact;
        $contact->user_id = $request->user()->id;
        $contact->save();

        return redirect()->route('contacts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Contacts $contact
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function show(Contacts $contact)
    {
        $this->authorize('view', $contact);

        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contacts $contact
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function edit(Contacts $contact)
    {
        $this->authorize('update', $contact);

        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreContacts $request
     * @param Contacts $contact
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(StoreContacts $request, Contacts $contact)
    {
        $this->authorize('update', $contact);

        $contact->update($request->all());
        return redirect()->route('contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contacts $contact
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Contacts $contact)
    {
        $this->authorize('delete', $contact);

        $contact->delete();
        return redirect()->route('contacts.index');
    }
}
