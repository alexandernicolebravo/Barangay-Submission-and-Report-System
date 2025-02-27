<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Weekly Report</title>
    <style>
        .field-row {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Create Weekly Report</h1>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.weekly-reports.store') }}" method="POST">
        @csrf

        <label for="month">Month:</label>
        <input type="text" name="month" id="month"><br>

        <label for="week_number">Week Number:</label>
        <input type="number" name="week_number" id="week_number"><br>

        <label for="num_of_clean_up_sites">Number of Clean-up Sites:</label>
        <input type="number" name="num_of_clean_up_sites" id="num_of_clean_up_sites"><br>

        <label for="num_of_participants">Number of Participants:</label>
        <input type="number" name="num_of_participants" id="num_of_participants"><br>

        <label for="num_of_barangays">Number of Barangays:</label>
        <input type="number" name="num_of_barangays" id="num_of_barangays"><br>

        <label for="total_volume">Total Volume:</label>
        <input type="number" name="total_volume" id="total_volume"><br>

        <label for="kalinisan_file_path">Kalinisan File Path:</label>
        <input type="text" name="kalinisan_file_path" id="kalinisan_file_path"><br>

        <div id="dynamic-fields">
            <div class="field-row">
                <label for="field_key[]">Field Key:</label>
                <input type="text" name="field_key[]" class="field-key">

                <label for="field_value[]">Field Value:</label>
                <input type="text" name="field_value[]" class="field-value">

                <button type="button" class="remove-field">Remove</button>
            </div>
        </div>

        <button type="button" id="add-field">Add Field</button><br>

        <button type="submit">Submit Report</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addFieldButton = document.getElementById('add-field');
            const dynamicFieldsContainer = document.getElementById('dynamic-fields');

            addFieldButton.addEventListener('click', function() {
                const newFieldRow = document.createElement('div');
                newFieldRow.classList.add('field-row');

                newFieldRow.innerHTML = `
                    <label for="field_key[]">Field Key:</label>
                    <input type="text" name="field_key[]" class="field-key">

                    <label for="field_value[]">Field Value:</label>
                    <input type="text" name="field_value[]" class="field-value">

                    <button type="button" class="remove-field">Remove</button>
                `;

                dynamicFieldsContainer.appendChild(newFieldRow);

                newFieldRow.querySelector('.remove-field').addEventListener('click', function() {
                    newFieldRow.remove();
                });

            });

            dynamicFieldsContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-field')) {
                    event.target.parentElement.remove();
                }
            });

        });
    </script>
</body>
</html>
