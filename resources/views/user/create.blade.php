<form action="{{ route('user.store') }}" method="post">
    @csrf

    <label for="input-name">Username</label>
    <input id="input-name" type="text" name="name" placeholder="John DOE" required>

    <label for="input-email">Email</label>
    <input type="email" name="email" placeholder="john.doe@example.com" required>

    <label for="input-password">Password</label>
    <input type="password" name="password" required>

    <button type="submit">Submit</button>
</form>
