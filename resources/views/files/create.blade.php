<form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div>
    <label for="file">Файл</label>
    <input type="file" name="file" id="file" required>
    @error('file') <div>{{ $message }}</div> @enderror
</div>

<div>
    <label for="comment">Коментар</label>
    <textarea name="comment" id="comment"></textarea>
    @error('comment') <div>{{ $message }}</div> @enderror
</div>

<div>
    <label for="delete_at">Дата видалення</label>
    <input type="date" name="delete_at" id="delete_at">
    @error('delete_at') <div>{{ $message }}</div> @enderror
</div>

<button type="submit">Завантажити</button>
</form>
