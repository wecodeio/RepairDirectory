@extends('admin.layout')

@section('content')

    <h2>{{ __('admin.form_title') }}</h2>
    <a class="btn btn-primary" href="{{ route('admin.business.edit') }}">Add repairer</a>

    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>{{ __('admin.name') }}</th>
            <th>{{ __('admin.address') }}</th>
            <th>{{ __('admin.postcode') }}</th>
            <th>{{ __('admin.categories') }}</th>
            <th>{{ __('admin.average_scores') }}</th>
            <th>{{ __('admin.publishing_status') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($businesses as $business)
            <tr onclick="window.document.location='{{ route('admin.business.edit', ['id' => $business->getUid()]) }}'"
                role="button">
                <td>{{ $business->getName() }}</td>
                <td>{{ $business->getAddress() }}</td>
                <td>{{ $business->getPostcode() }}</td>
                <td>{{ implode(', ', $business->getCategories()) }}</td>
                <td>{{ $business->getAverageScore() }}</td>
                <td>{{ $business->getPublishingStatus() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')
    <script defer>
        jQuery('table').DataTable();
    </script>
@endpush