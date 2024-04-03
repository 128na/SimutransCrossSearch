<div class="container">
    <div class="mx-2 alert alert-danger">
        @foreach ($errors->all() as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
</div>
