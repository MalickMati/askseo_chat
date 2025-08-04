@extends('layouts.admin')

@section('title', 'Admin | Manage Tasks')

@section('main_content')
    <x-admin_sidebar :activePage="$activePage" :name="$name" :email="$email" :img="$img"></x-admin_sidebar>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">
                <svg width="28" viewBox="0 0 321.094 321.094" xml:space="preserve" fill="currentcolor">
                    <path
                        d="M277.54 19.907h-63.18V7.5a7.5 7.5 0 0 0-7.5-7.5h-92.625a7.5 7.5 0 0 0-7.5 7.5v12.407h-63.18a7.5 7.5 0 0 0-7.5 7.5v286.187a7.5 7.5 0 0 0 7.5 7.5h233.984a7.5 7.5 0 0 0 7.5-7.5V27.407a7.5 7.5 0 0 0-7.499-7.5M121.735 15h77.625v24h-77.625zm148.306 291.094H51.056V34.907h55.68V46.5a7.5 7.5 0 0 0 7.5 7.5h92.625a7.5 7.5 0 0 0 7.5-7.5V34.907h55.68z" />
                    <path
                        d="M109.397 86.5h-35a7.5 7.5 0 0 0-7.5 7.5v35a7.5 7.5 0 0 0 7.5 7.5h35a7.5 7.5 0 0 0 7.5-7.5V94a7.5 7.5 0 0 0-7.5-7.5m-7.5 35h-20v-20h20zm25-27.5v35a7.5 7.5 0 0 0 7.5 7.5h112.301a7.5 7.5 0 0 0 7.5-7.5V94a7.5 7.5 0 0 0-7.5-7.5H134.397a7.5 7.5 0 0 0-7.5 7.5m15 7.5h97.301v20h-97.301zm-32.5 54h-35a7.5 7.5 0 0 0-7.5 7.5v35a7.5 7.5 0 0 0 7.5 7.5h35a7.5 7.5 0 0 0 7.5-7.5v-35a7.5 7.5 0 0 0-7.5-7.5m-7.5 35h-20v-20h20zm144.801-35H134.397a7.5 7.5 0 0 0-7.5 7.5v35a7.5 7.5 0 0 0 7.5 7.5h112.301a7.5 7.5 0 0 0 7.5-7.5v-35a7.5 7.5 0 0 0-7.5-7.5m-7.5 35h-97.301v-20h97.301zm-129.801 34h-35a7.5 7.5 0 0 0-7.5 7.5v35a7.5 7.5 0 0 0 7.5 7.5h35a7.5 7.5 0 0 0 7.5-7.5v-35a7.5 7.5 0 0 0-7.5-7.5m-7.5 35h-20v-20h20zm144.801-35H134.397a7.5 7.5 0 0 0-7.5 7.5v35a7.5 7.5 0 0 0 7.5 7.5h112.301a7.5 7.5 0 0 0 7.5-7.5v-35a7.5 7.5 0 0 0-7.5-7.5m-7.5 35h-97.301v-20h97.301z" />
                </svg>
                Tasks Management
            </h1>
        </div>

        <div class="table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Completed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->user->name }}</td>
                            <td>
                                <span class="badge {{ $task->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td>{{ $task->due_date }}</td>
                            <td>{{ $task->completed_at ?? '—' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-delete" data-task-id="{{ $task->id }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6zm-9 5v6m4-6v6"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted">No tasks assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
<script>
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-delete')) {
                const button = e.target.closest('.btn-delete');
                const taskId = button.getAttribute('data-task-id');

                if (confirm("Are you sure you want to delete this task?")) {
                    fetch('/delete-task', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ id: taskId })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showNotificationToast(1, 'Task deleted successfully. Reloading...');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                showNotificationToast(3, 'Failed to delete task', 4000);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showNotificationToast(3, 'Server error while deleting', 4000);
                        });
                }
            }
        });
    </script>
@endsection