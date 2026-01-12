@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="font-bold text-2xl text-slate-800">User Management</h1>
        <p class="text-slate-500">Manage user accounts and access</p>
    </div>
    <form action="{{ route('admin.users.index') }}" method="GET" class="relative">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="pl-9 pr-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
        <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
    </form>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm role='alert'">
    <p>{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm role='alert'">
    <p>{{ session('error') }}</p>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                    <th class="p-4 text-xs text-center font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="p-4 text-xs text-right font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-4 text-slate-500">#{{ $user->id }}</td>
                    <td class="p-4 font-bold text-slate-700">{{ $user->name }}</td>
                    <td class="p-4 text-slate-600">{{ $user->email }}</td>
                    <td class="p-4">
                        @if($user->role === 'admin')
                            <span class="bg-purple-100 text-purple-700 text-xs px-2 py-1 rounded font-bold uppercase">Admin</span>
                        @else
                            <span class="bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded font-bold uppercase">User</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if($user->status === 'suspended')
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded font-bold uppercase">Suspended</span>
                        @else
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded font-bold uppercase">Active</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        @if($user->role !== 'admin')
                        <div class="flex items-center justify-end gap-3">
                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-yellow-500 hover:text-yellow-600 transition" title="{{ $user->status === 'suspended' ? 'Activate' : 'Suspend' }}">
                                    <i class="fas {{ $user->status === 'suspended' ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                        @else
                         <span class="text-slate-300"><i class="fas fa-lock"></i></span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-200 bg-slate-50">
        {{ $users->links() }}
    </div>
</div>
@endsection
