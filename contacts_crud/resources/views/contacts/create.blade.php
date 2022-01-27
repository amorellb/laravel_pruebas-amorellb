@include('layouts.plantilla')

<main>
    <h2 class="text-xl">Añadir nuevos contactos a la Agenda</h2>
    <form class="ml-5 mt-5" method="POST" enctype="multipart/form-data" action="{{ route('contacts.store') }}">
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
        <label for="birth_date"> Birth date:
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="date" name="birth_date" value="{{old('birth_date')}}"/>
        </label><br>
        <label for="email"> Contact email:
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="email" value="{{old('email')}}"
                   placeholder="bernat@email.com"/>
        </label><br>
        <label for="phone"> Contact phone:
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="phone" value="{{old('phone')}}"
                   placeholder="654321234"/>
        </label>
        @error('phone')
        <br>
        <small>*{{$message}}</small>
        <br>
        @enderror
        <br>
        <label for="country">Country: </label>
        <select class="border-2 border-solid border-gray-100 rounded-full px-2" name="country" id="country">
            <option value="Spain" @if (old('country') === 'spain') selected @endif>Spain</option>
            <option value="England" @if (old('country') === 'england') selected @endif>England</option>
            <option value="Italy" @if (old('country') === 'italy') selected @endif>Italy</option>
            <option value="Germany" @if (old('country') === 'germany') selected @endif>Germany</option>
            <option value="France" @if (old('country') === 'france') selected @endif>France</option>
        </select>
        <br>
        <label for="address"> Contact address:
            <br>
            <textarea class="border-2 border-solid border-gray-100 rounded px-2" name="address" placeholder="Address 123, street">{{old('address')}}</textarea>
        </label><br>
        <label for="job_contact"> Job contact?:
            <br>
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio" name="job_contact_true" value="true" checked/> True
            <br>
            <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio" name="job_contact_false" value="false" checked="checked"/> False
        </label><br>
        <button
            class="text-green-400 no-underline border-solid border-2 border-green-400 rounded p-1 px-5 ml-5 mt-5 hover:bg-green- 400 hover:text-white"
            type="submit" name="add">➕ Add Contact
        </button>
    </form>
</main>
