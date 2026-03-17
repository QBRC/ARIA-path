@extends('layouts.app')

@section('page-title')
    {{ __('API Health Check') }}
@endsection

@section('style')
    <link href="{{ asset('css/healthcheck.css') }}" rel="stylesheet">
@endsection

@section('content')

    @if(session('succeed'))
        <div class="alert alert-success flash mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <strong>{{ session('succeed') }}</strong>
        </div>
    @endif

    <div class="card shadow mb-4">
    <div class="card-header py-3">
        {{-- Add any header content if necessary --}}
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <button id="runAllApisBtn" class="btn btn-primary" onclick="runAllApis()">
                Test All APIs
            </button>
            <span id="runAllSpinner" class="spinner-border text-primary d-none" role="status"></span> 
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>API Group</th>
                        <th>API Names</th>
                        <th>Health Check URL</th>
                        <th>Server Test</th>
                        <th>Browser Test</th>
                        <th>Last Checked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apiGroups as $api)
                        <tr data-group-name="{{ $api['group_name'] }}">
                            <td class="group-name-fixed-width">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-question-circle text-secondary mr-2 status-icon"></i>
                                    <span>{{ $api['group_name'] }}</span>
                                </div>
                            </td>
                            <td class="api-name-fixed-width">{{ $api['api_names'] }}</td>
                            <td class="api-url-fixed-width">{{ $api['health_check_url'] }}</td>
                            <td class="last-response-column">{{ $api['server_test'] ?? 'N/A' }}</td>
                            <td class="browser-test-column">{{ $api['browser_test'] ?? 'N/A' }}</td>
                            <td class="last-checked-column last-checked-fixed-width">{{ $api['last_checked'] ?? 'Never' }}</td>
                            <td>
                                <button class="btn btn-primary retry-btn" onclick="retryHealthCheck('{{ $api['group_name'] }}', this)">Check</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



<script>
    let completedApis = 0;
    const totalApis = document.querySelectorAll('.retry-btn').length; 
    const baseUrl = "{{asset('')}}";
    function retryHealthCheck(groupName, button) {
        button.setAttribute('disabled', 'true');
        const spinner = document.createElement('span');
        spinner.classList.add('spinner-border', 'spinner-border-sm', 'text-light');
        button.innerHTML = '';  // Clear the button text
        button.appendChild(spinner);  // Add spinner to the button
        // Step 1: Server-to-Server Test
        fetch(baseUrl+`api/health-checks/${groupName}/check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(response => response.json())
        .then(data => {
            console.log('data.status'+JSON.stringify(data, null, 2))
            if (data.status === 'Success') {
                const row = document.querySelector(`tr[data-group-name="${groupName}"]`);
                if (row) {
                    const statusIcon = row.querySelector('.status-icon');
                    if (data.status === 'Success' && data.status_code === 200) {
                        statusIcon.className = 'fas fa-check-circle text-success mr-2 status-icon'; 
                    } else {
                        statusIcon.className = 'fas fa-times-circle text-danger mr-2 status-icon'; 
                    }
                    row.querySelector('.last-checked-column').textContent = new Date().toLocaleString(); 
                    const environment = data.environment ? JSON.stringify(data.environment) : ''; // Convert environment to string if it's an object
                    const details = data.details || '';

                    const combinedText = environment && details ? `${environment} - ${details}` : environment || details; 
                    row.querySelector('.last-response-column').textContent = combinedText;
                    return browserHealthCheck(row.querySelector('.api-url-fixed-width').textContent, row);
                }
            } else {
                alert(data.message || 'Health check failed.');
            }

        })
        .catch(error => {
            alert('An error occurred.');
            console.error(error);
            button.innerHTML = 'Retry';
            button.removeAttribute('disabled');
            completedApis++;
            if (completedApis === totalApis) {
                document.getElementById('runAllApisBtn').removeAttribute('disabled');
            }
        })
        .finally(() => {
            setTimeout(() => {
                spinner.classList.add('d-none');  
                button.classList.remove('d-none'); 
                button.innerHTML = 'Retry';
                button.removeAttribute('disabled');
                completedApis++;
                if (completedApis === totalApis) {
                    document.getElementById('runAllApisBtn').removeAttribute('disabled');
                }
                button.removeAttribute('disabled');
            }, 200);  
        });
    }

    // Step 2: Browser Test Function
    function browserHealthCheck(url, row) {
        return fetch(url)
            .then(response => {
                const browserTestColumn = row.querySelector('.browser-test-column');
                console.log("browser-response: "+ response)
                if (response.ok) {
                    browserTestColumn.innerHTML = '<span class="badge badge-success">Success</span>';
                } else {
                    browserTestColumn.innerHTML = `<span class="badge badge-warning">HTTP ${response.status}</span>`;
                }
            })
            .catch(error => {
                console.error('Browser test failed:', error);
                row.querySelector('.browser-test-column').innerHTML = '<span class="badge badge-danger">Error</span>';
            });
    }

    // Function to run all APIs
    function runAllApis() {
        document.getElementById('runAllApisBtn').setAttribute('disabled', 'true');
        completedApis = 0;
        
        // Get all group names from the table
        const groupNames = Array.from(document.querySelectorAll('tr[data-group-name]'))
            .map(row => row.getAttribute('data-group-name'));

        groupNames.forEach((groupName, index) => {
            setTimeout(() => {
                // Call the retry function for each group name
                const button = document.querySelector(`tr[data-group-name="${groupName}"] .retry-btn`);
                retryHealthCheck(groupName, button);
            }, index * 1000);  // Delay each API check by 1 second to make it feel sequential
        });
    }
</script>
@endsection
