<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>

    <!-- Display Messages -->
    @if(session('success'))
        <p class="text-green-500">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif

    <!-- Create User Form -->
    <h2 class="text-xl mt-4">Create User</h2>
    <form action="{{ route('admin.store') }}" method="POST" class="space-y-2">
        @csrf
        <label class="block">Name:</label>
        <input type="text" name="name" required class="border p-1 w-full">

        <label class="block">Email:</label>
        <input type="email" name="email" required class="border p-1 w-full">

        <label class="block">Password:</label>
        <input type="password" name="password" required class="border p-1 w-full">

        <label class="block">Role:</label>
        <select name="role" id="role-select" required class="border p-1 w-full">
            <option value="cluster">Cluster</option>
            <option value="barangay">Barangay</option>
        </select>

        <div id="cluster-selection" class="mt-2 hidden">
            <label class="block">Assign to Cluster:</label>
            <select name="cluster_id" class="border p-1 w-full">
                <option value="">-- Select Cluster --</option>
                @foreach($clusters as $cluster)
                    <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Create</button>
    </form>



    <!-- Tab Navigation -->
    <div class="mt-6">
        <h2 class="text-xl">Existing Users</h2>

        <div class="flex space-x-4 mt-4 border-b">
            @foreach($clusters as $index => $cluster)
                <button class="tab-button px-4 py-2" data-tab="tab-{{ $cluster->id }}"
                    :class="{ 'border-b-2 border-blue-500': activeTab === 'tab-{{ $cluster->id }}' }">
                    {{ $cluster->name }}
                </button>
            @endforeach
        </div>

        <!-- Tab Content -->
        @foreach($clusters as $cluster)
            <div id="tab-{{ $cluster->id }}" class="tab-content mt-4 hidden">
                <h3 class="text-lg font-semibold">{{ $cluster->name }} ({{ $cluster->email }})</h3>
                <span class="{{ $cluster->is_active ? 'text-green-500' : 'text-red-500' }}">
                    {{ $cluster->is_active ? 'Active' : 'Inactive' }}
                </span>

                <!-- List Barangays Assigned to This Cluster -->
                <h4 class="text-md font-semibold mt-4">Barangays under {{ $cluster->name }}</h4>
                <ul class="list-disc ml-6">
                    @foreach($barangays->where('cluster_id', $cluster->id) as $barangay)
                        <li class="flex justify-between items-center">
                            <span>{{ $barangay->name }} ({{ $barangay->email }}) -
                                <span class="{{ $barangay->is_active ? 'text-green-500' : 'text-red-500' }}">
                                    {{ $barangay->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </span>
                            <form method="POST" action="{{ route('admin.users.destroy', $barangay->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="text-red-500 confirm-deactivate" data-id="{{ $barangay->id }}">
                                    {{ $barangay->is_active ? 'Disable' : 'Reactivate' }}
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabButtons = document.querySelectorAll(".tab-button");
            const tabContents = document.querySelectorAll(".tab-content");

            // Set default active tab (first cluster)
            if (tabButtons.length > 0) {
                tabButtons[0].classList.add("border-b-2", "border-blue-500");
                tabContents[0].classList.remove("hidden");
            }

            tabButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const targetTab = this.getAttribute("data-tab");

                    // Hide all tabs
                    tabContents.forEach(tab => tab.classList.add("hidden"));
                    tabButtons.forEach(btn => btn.classList.remove("border-b-2", "border-blue-500"));

                    // Show selected tab
                    document.getElementById(targetTab).classList.remove("hidden");
                    this.classList.add("border-b-2", "border-blue-500");
                });
            });

            // Confirmation for deactivation
            document.querySelectorAll('.confirm-deactivate').forEach(button => {
                button.addEventListener('click', async function (event) {
                    event.preventDefault();
                    const userId = this.dataset.id;
                    const response = await fetch(`/admin/users/${userId}/confirm-deactivation`);
                    const data = await response.json();
                    if (data.confirm && confirm(data.confirm)) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>

</body>
</html>
