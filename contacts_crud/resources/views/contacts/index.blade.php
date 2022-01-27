@include('layouts.plantilla')

<main>
    <section class="antialiased bg-gray-100 text-gray-600 h-screen px-4">
        <div class="mb-10 ">
            <div class="mb-5">
                <h2 class="text-xl">Contacts</h2>
            </div>
            @auth
                @can('create', \App\Models\Contact::class)
                    <div class="mt-5">
                        <a class="text-green-400 no-underline border-solid border-2 border-green-400 rounded p-1 ml-5 hover:bg-green-400 hover:text-white"
                           href="{{ route('contacts.create') }}">➕ Add Contact</a>
                    </div>
                @endcan
            @endauth
        </div>


        <div class="w-full max-w-7xl mx-auto bg-white shadow-lg rounded border border-gray-200">
            <div class="p-3">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead class="text-xs font-semibold uppercase text-gray-400">
                        <tr>
                            @auth
                                @can('viewAll', \App\Models\Contact::class)
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">User Name</div>
                                    </th>
                                @endcan
                            @endauth
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Name</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Birth date</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Email</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Phone</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Country</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Address</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Job contact?</div>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">

                        @foreach ($contacts as $contact)
                            <tr>
                                @auth
                                    @can('viewAll', \App\Models\Contact::class)
                                        <td class="p-2 whitespace-nowrap">{{ $contact->user->name }}</td>
                                    @endcan
                                @endauth
                                <td class="p-2 whitespace-nowrap">{{ $contact->name }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->birth_date }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->email }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->phone }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->country }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->address }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->job_contact }}</td>
                                <td class="p-2 whitespace-nowrap">
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                                        @auth
                                            @can('view', $contact)
                                                <a class="text-blue-400 no-underline border-solid border-2 border-blue-400 rounded p-1 px-3 ml-5 hover:bg-blue-400 hover:text-white"
                                                   href="{{ route('contacts.show', $contact) }}">👀 Show</a>
                                            @endcan
                                        @endauth
                                        @auth
                                            @can('update', $contact)
                                                <a class="text-orange-400 no-underline border-solid border-2 border-orange-400 rounded p-1 px-3 ml-5 hover:bg-orange-400 hover:text-white"
                                                   href="{{ route('contacts.edit', $contact) }}">📝 Edit</a>
                                            @endcan
                                        @endauth
                                        @csrf
                                        @method('DELETE')
                                        @auth
                                            @can('delete', $contact)
                                                <button type="submit"
                                                        class="text-red-400 no-underline border-solid border-2 border-red-400 rounded p-1 px-3 ml-5 hover:bg-red-400 hover:text-white">
                                                    💥 Delete
                                                </button>
                                            @endcan
                                        @endauth
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
