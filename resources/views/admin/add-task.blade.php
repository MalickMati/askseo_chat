@extends('layouts.admin')

@section('title', 'Admin | Assign Tasks')

@section('css')
    <style>
        .form-heading {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .task-form-section {
            background-color: var(--secondary-bg);
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .task-form .form-group {
            margin-bottom: 1.2rem;
            display: flex;
            flex-direction: column;
        }

        .task-form label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        .task-form input,
        .task-form select,
        .task-form textarea {
            padding: 0.75rem 1rem;
            background-color: var(--primary-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .task-form input:focus,
        .task-form select:focus,
        .task-form textarea:focus {
            border-color: var(--accent-1);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-2), var(--accent-1));
        }
    </style>
@endsection

@section('main_content')
    <x-admin_sidebar :activePage="$activePage" :name="$name" :email="$email" :img="$img" />

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">
                <svg width="28" height="28" viewBox="0 0 36 36" fill="currentcolor">
                    <path class="clr-i-solid clr-i-solid-path-1"
                        d="M29.29 4.95h-7.2a4.31 4.31 0 0 0-8.17 0H7a1.75 1.75 0 0 0-2 1.69v25.62a1.7 1.7 0 0 0 1.71 1.69h22.58A1.7 1.7 0 0 0 31 32.26V6.64a1.7 1.7 0 0 0-1.71-1.69m-18 3a1 1 0 0 1 1-1h3.44v-.63a2.31 2.31 0 0 1 4.63 0V7h3.44a1 1 0 0 1 1 1v1.8H11.25Zm14.52 9.23-9.12 9.12-5.24-5.24a1.4 1.4 0 0 1 2-2l3.26 3.26 7.14-7.14a1.4 1.4 0 1 1 2 2Z" />
                    <path fill="none" d="M0 0h36v36H0z" />
                </svg>
                Assign Task
            </h1>
        </div>

        <div class="task-form-section">
            <h2 class="form-heading">Assign New Task</h2>
            <form id="assignTaskForm" class="task-form" method="POST" action="{{ route('assign.task') }}">
                @csrf
                <div class="form-group">
                    <label for="assigned_to">Assign To</label>
                    <select id="assigned_to" name="assigned_to" required>
                        <option value="" disabled selected>Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Task Title</label>
                    <input type="text" id="title" name="title" required />
                </div>

                <div class="form-group">
                    <label for="description">Task Description</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date" required />
                </div>

                <button type="submit" class="btn-primary">Assign Task</button>
            </form>
        </div>
    </div>

@endsection

@section('js')
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showNotificationToast(1, @json(session('success')), 4000);
            });
        </script>
    @endif
@endsection