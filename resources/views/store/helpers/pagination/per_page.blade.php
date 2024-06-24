<select name="postperpage" class="form-select" list-control="per-page">
    @foreach([2, 5, 10, 20, 50, 100] as $number)
        <option value="{{ $number }}" {{ $perPage == $number ? 'selected' : '' }}>{{ $number }}</option>
    @endforeach
</select>