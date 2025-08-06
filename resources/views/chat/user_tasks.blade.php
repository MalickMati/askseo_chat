@extends('layouts.chat-settings')

@section('title', 'User | Tasks')

@section('css')
    <style>
        .task-item {
            display: flex;
            align-items: flex-start;
            background-color: var(--secondary-bg);
            border: 2px solid var(--accent-1);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            position: relative;
            gap: 1rem;
        }

        .task-item:has(input[type="checkbox"]:checked) {
            background-color: rgba(0, 255, 157, 0.08);
            border-left: 4px solid var(--success);
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, border 0.3s ease;
        }

        .task-item:has(input[type="checkbox"]:not(:checked)) {
            background-color: rgba(255, 56, 96, 0.08);
            border-left: 4px solid var(--error);
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, border 0.3s ease;
        }

        .task-item input[type="checkbox"] {
            display: none;
        }

        .task-item .task-content {
            display: flex;
            flex-direction: column;
            color: var(--text-primary);
            width: 100%;
        }

        .task-item strong {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .task-item .text-muted {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .task-item .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 999px;
            font-weight: 500;
            width: fit-content;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .badge-success {
            background-color: var(--success);
            color: var(--primary-bg);
        }

        .badge-warning {
            background-color: var(--away);
            color: var(--primary-bg);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .btn-primary {
            background-color: var(--accent-1);
            color: var(--primary-bg);
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--accent-2);
        }

        .no-tasks-message {
            padding: 1rem;
            background-color: var(--secondary-bg);
            color: var(--text-secondary);
            text-align: center;
            border-radius: 0.5rem;
            border-left: 4px solid var(--accent-1);
            font-size: 0.95rem;
        }
    </style>
@endsection

@section('form-section')
    <x-chat-settings.settings-header :message="'Check off tasks you have completed'" :heading="'My Assigned Tasks'" />

    <form class="settings-form" id="taskForm" autocomplete="off">
        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Assigned Tasks
            </div>

            @forelse ($tasks as $task)
                <label class="task-item">
                    <input type="checkbox" name="tasks[]" value="{{ $task['id'] }}" {{ $task['status'] === 'completed' ? 'checked disabled' : '' }}>

                    <div class="task-content">
                        <strong>{{ $task['title'] }}</strong>
                        <p class="text-muted">{{ $task['description'] }}</p>
                        <span class="badge {{ $task['status'] === 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($task['status']) }}
                        </span>
                    </div>
                </label>
            @empty
                <p class="no-tasks-message text-muted">You have no tasks assigned.</p>
            @endforelse
            @if (!empty($tasks) && count($tasks))
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            @endif
        </div>
    </form>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
@endsection

@section('js')
    <script>
        const backButton = document.getElementById('backButton');
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('taskForm');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const selectedTasks = formData.getAll('tasks[]');

                fetch("/tasks/update", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ tasks: selectedTasks })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotificationToast(1, data.message || 'Tasks updated successfully');
                        } else {
                            showNotificationToast(2, data.message || 'Failed to update!');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showNotificationToast(3, 'Something went wrong while updating tasks');
                    });
            });
            backButton.addEventListener('click', function () {
                window.location.href = '/chat';
            });
        });

    </script>
@endsection