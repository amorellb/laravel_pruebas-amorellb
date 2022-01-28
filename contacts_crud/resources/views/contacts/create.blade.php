@include('layouts.plantilla')

<main class="mt-5">
    <div class="w-full max-w-xl mx-auto bg-white shadow-lg rounded border border-gray-200">
        <h2 class="text-xl m-5">Add new contacts to your Contacts list</h2>
        @if ($errors->any())
            <div class="mx-auto max-w-md border-2 border-solid border-red-600 bg-red-300 rounded text-center">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form class="m-5" method="POST" enctype="multipart/form-data" action="{{ route('contacts.store') }}">
            @csrf
            <label for="name"> Contact name:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="name"
                       value="{{old('name')}}" placeholder="Bernat Smith"/>
            </label>
            @error('name')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="birth_date"> Birth date:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="date" name="birth_date"
                       value="{{old('birth_date')}}"/>
            </label>
            <br>
            <br>
            <label for="email"> Contact email:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="email"
                       value="{{old('email')}}"
                       placeholder="bernat@email.com"/>
            </label>
            <br>
            <br>
            <label for="phone"> Contact phone:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="number" name="phone"
                       value="{{old('phone')}}"
                       placeholder="654321234"/>
            </label>
            @error('phone')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="country">Country: </label>
            <select class="border-2 border-solid border-gray-100 rounded-full px-2" name="country" id="country">
                <option value="England" @if (old('country') === 'England') selected @endif>England</option>
                <option value="Spain" @if (old('country') === 'Spain') selected @endif>Spain</option>
                <option value="Italy" @if (old('country') === 'Italy') selected @endif>Italy</option>
                <option value="Germany" @if (old('country') === 'Germany') selected @endif>Germany</option>
                <option value="France" @if (old('country') === 'France') selected @endif>France</option>
            </select>
            <br>
            <br>
            <label for="address"> Contact address:
                <br>
                <textarea class="border-2 border-solid border-gray-100 rounded px-2" name="address"
                          placeholder="Address 123, street">{{old('address')}}</textarea>
            </label>
            <br>
            <br>
            <label for="job_contact_true"> Job contact?:
                <br>
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio"
                       name="job_contact_yes" value="yes" checked/> Yes
            </label>
            <label for="job_contact_false">
                <br>
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio"
                       name="job_contact_yes" value="no" checked/> No
            </label>
            <br>
            <br>
            <label for="terms">
                <input type="checkbox" id="terms" name="terms"> Accept terms and conditions.
            </label>
            <br>
            <button
                class="text-green-400 no-underline border-solid border-2 border-green-400 rounded p-1 px-5 ml-5 mt-5 hover:bg-green-400 hover:text-white"
                type="submit" name="add">➕ Add Contact
            </button>
        </form>
    </div>
</main>
