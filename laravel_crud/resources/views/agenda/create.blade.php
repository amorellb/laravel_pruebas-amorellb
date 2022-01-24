@include('layouts.plantilla')

<main>
    <h2 class="text-xl">Añadir nuevos contactos a la Agenda</h2>
    <form class="ml-5 mt-5" method="POST" enctype="multipart/form-data" action="{{ route('agenda.store') }}">
        @csrf
        <label for="name"> Contact name:
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="name" value="{{old('name')}}" placeholder="Bernat Smith"/>
        </label>
        @error('name')
        <br>
        <small>*{{$message}}</small>
        <br>
        @enderror
        <br>
        <label for="email"> Contact email:
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="email" value="{{old('email')}}" placeholder="bernat@email.com"/>
        </label><br>
        <label for="phone"> Contact phone:
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="phone" value="{{old('phone')}}" placeholder="654321234"/>
        </label>
        @error('phone')
        <br>
        <small>*{{$message}}</small>
        <br>
        @enderror
        <br>
        <label for="address"> Contact address:
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="address" value="{{old('address')}}" placeholder="Address 123, street"/>
        </label><br>
        <button class="text-green-400 no-underline border-solid border-2 border-green-400 rounded p-1 px-5 ml-5 mt-5 hover:bg-green-400 hover:text-white" type="submit" name="add">➕ Add Contact</button>
    </form>
</main>
