<div class="container">
    <h1>Profilo Utente</h1>

    <p><strong>Nome:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Registrato il:</strong> {{ $user->created_at->format('d/m/Y') }}</p>

    <a href="{{ route('user.index') }}" class="btn btn-secondary">Torna alla lista</a>
</div>
@endsection