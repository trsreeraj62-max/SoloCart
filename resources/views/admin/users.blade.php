@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-bold text-2xl">Users</h1>
</div>
<div class="card p-0" style="overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
            <tr>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">ID</th>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">Name</th>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">Email</th>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">Role</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Joined</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="border-bottom: 1px solid var(--border);">
                <td class="p-4 text-muted">#{{ $user->id }}</td>
                <td class="p-4 font-bold">{{ $user->name }}</td>
                <td class="p-4">{{ $user->email }}</td>
                <td class="p-4">
                    <span class="inline-block px-2 py-1 rounded text-xs font-bold {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="p-4 text-right text-sm text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="p-4 text-right">
                    <button class="text-primary font-bold hover:underline">Edit</button>
                    @if($user->role !== 'admin')
                        <button class="text-red-500 font-bold hover:underline ml-2">Block</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
