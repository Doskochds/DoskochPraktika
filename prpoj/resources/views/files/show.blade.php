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


    <form action="{{ route('file.generate.one', ['file' => $file->id]) }}" method="POST" class="mt-4">
        @csrf
        <div class="form-group">
            <label for="count">Кількість посилань:</label>
            <input type="number" id="count" name="count" class="form-control" min="1" max="50" value="5">
        </div>
        <button type="submit" class="btn btn-primary">Згенерувати посилання</button>
    </form>

    <h2 class="mt-4">Посилання для файлу</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Посилання</th>
            <th>Дата створення</th>
            <th>Дія</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($file->oneTimeLinks as $link)
            <tr id="link-{{ $link->token }}">
                <td><a href="{{ route('file.view.one', ['token' => $link->token]) }}" target="_blank">{{ route('file.view.one', ['token' => $link->token]) }}</a></td>
                <td>{{ $link->created_at->toDateTimeString() }}</td>
                <td>
                    <form action="{{ route('file.delete.link', ['token' => $link->token]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Видалити</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <a href="{{ route('files.index') }}" class="btn btn-secondary mt-4">Назад до списку</a>
@endsection


