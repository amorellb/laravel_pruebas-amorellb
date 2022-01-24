@include('layouts.plantilla')

<main>
    <ul class="list-none ml-5 my-5">
        <li class="font-bold">
            <div class="flex">
                {{$contact->name}}
                <a class="pl-1 no-underline" href="{{ route('agenda.edit', $contact) }}">📝</a>
                <form class="w-1" action="{{ route('agenda.destroy', $contact) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="b-0 pl-1 background-none">💥</button>
                </form>
            </div>
        </li>
        <ul class="ml-5">
            <li>{{$contact->email}}</li>
            <li>{{$contact->phone}}</li>
            <li>{{$contact->address}}</li>
        </ul>
    </ul>
</main>
