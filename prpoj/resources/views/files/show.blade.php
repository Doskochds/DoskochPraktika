@php  @endphp
@extends('layouts.app')

@section('content')
    <h1>Перегляд файлу</h1>


    @php
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $fileExtension = isset($file) ? strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)) : null;
    @endphp

    @if(in_array($fileExtension, $imageExtensions))
        <img src="{{ asset('storage/' . $file->file_name) }}" alt="Файл">
    @endif

    <p><strong>Ім'я файлу:</strong> {{ basename($file->file_name) }}</p>
    <p><strong>Коментар:</strong> {{ $file->comment }}</p>

    @if($file->delete_at)
        <p><strong>Дата видалення:</strong> {{ $file->delete_at }}</p>
    @endif


    <form action="{{ route('files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Ви впевнені?');">
        @csrf
        @method('DELETE')
        <button type="submit">Видалити файл</button>
    </form>
    <p>Посилання на файл: <a href="{{ route('files.view', $file->id) }}" target="_blank">{{ route('files.view', $file->id) }}</a></p>
    <p>Кількість переглядів: {{ $file->views }}</p>

    <a href="{{ route('files.index') }}">Назад до списку</a>
@endsection
