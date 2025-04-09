@extends('layouts.app')

@section('content')
    <h1>Список файлів</h1>

    <p>Кількість файлів: {{ $files->count() }}</p>


    <ul>
        @foreach ($files as $file)
            <li>
                <a href="{{ route('files.show', $file->id) }}">{{ $file->file_name }}</a>
                - Коментар: {{ $file->comment }}
            </li>
        @endforeach
    </ul>
@endsection
