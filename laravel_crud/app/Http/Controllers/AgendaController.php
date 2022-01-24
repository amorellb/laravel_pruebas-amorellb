<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgenda;
use App\Models\Agenda;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Agenda::class);

//        Ejecutamos la autorización definida en AuthServiceProvider.php
//        1)
//        $this->authorize('access', 403);
//        2)
//        abort_unless(Gate::allows('access'), 403);
//        3)
//        if (Gate::allows('access')) {
//            $contacts = Agenda::all();
//            return view('agenda.index', compact('contacts'));
//        }
//        Abort(403);

//        Filtramos los contactos por usuario sin usar ni Gate ni Polices
        $contacts = Agenda::where('user_id', Auth::id())->latest()->paginate(5);

//        $contacts = Agenda::all();
        return view('agenda.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Agenda::class);

        return view('agenda.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAgenda $request
     * @return RedirectResponse
     */
    public function store(StoreAgenda $request): RedirectResponse
    {
//        Validación del formulario
//        $request->validate([
//            'name' => 'required',
//            'phone' => 'required|size:9',
//        ]);

//        Podemos almacenar los datos del formulario de tres formas

        //      Generamos el slug
        $request['slug'] = Str::slug($request->name, '-');

//        1)
        $contact = new Agenda();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->address = $request->address;
        $contact->slug = $request->slug;
        $contact->user_id = $request->user()->id;
        $contact->save();

//        2)
//        Agenda::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'phone' => $request->phone,
//            'address' => $request->address
//        ]);

//        3) Para utilizar este método debemos añadir la propiedad fillable al modelo Agenda.php
//        Agenda::create($request->all());

//        Podemos redireccionar a la vista del contacto que acabamos de crear
//        Podemos pasar el id del contact, per Laravel ya nos redirecciona a este sin necesidad de especificarlo
//        return redirect()->route('agenda.show', $contact);
        return redirect()->route('agenda.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Agenda $contact
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function show(Agenda $contact)
    {
        $this->authorize('view', $contact);

        return view('agenda.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Agenda $contact
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function edit(Agenda $contact)
    {
        $this->authorize('update', $contact);

        return view('agenda.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreAgenda $request
     * @param Agenda $contact
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(StoreAgenda $request, Agenda $contact): RedirectResponse
    {
//        Validación del formulario
//        $request->validate([
//            'name' => 'required',
//            'phone' => 'required|size:9',
//        ]);

//        $contact->name = $request->name;
//        $contact->email = $request->email;
//        $contact->phone = $request->phone;
//        $contact->address = $request->address;
//        $contact->save();

        $this->authorize('update', $contact);

        $contact->update($request->all());

        return redirect()->route('agenda.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Agenda $contact
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Agenda $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        $contact->delete();
        return redirect()->route('agenda.index');
    }
}
