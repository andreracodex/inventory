@extends('layouts.app')
@push('script')
    <script>
        $(document).ready(function() {
            var table = $('#unitsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('unit.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<div class="btn-group" role="group" aria-label="Units Actions">' +
                                '<button class="btn btn-sm btn-view btn-info" title="View Data" data-id="' +
                                row.id +
                                '" onclick="viewUnits(' + row.id +
                                ')"><i class="fa-solid fa-eye"></i></button>' +
                                '<button class="btn btn-sm btn-duplicate btn-secondary" title="Duplicate Data" data-id="' +
                                row.id + '" onclick="duplicateUnits(' + row.id +
                                ')"><i class="fa-solid fa-copy"></i></button>' +
                                '<button class="btn btn-sm btn-edit btn-warning" title="Edit Data" data-id="' +
                                row
                                .id + '" onclick="editUnits(' + row.id +
                                ')"><i class="fa-solid fa-pencil"></i></button>' +
                                '<button class="btn btn-sm btn-delete btn-danger" title="Delete Data" onclick="deleteUnits(' +
                                row.id + ')"><i class="fa-solid fa-trash"></i></button>' +
                                '</div>';
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: "<'row'<'col-sm-12'B>>" + // Buttons in one row
                    "<'row'<'col-sm-12'<'filter-group'>fl>>" + // Search and length control in the same row
                    "<'row'<'col-sm-12'tr>>" + // Table
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", // Info and pagination
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fa-solid fa-copy"></i>',
                        className: 'buttons-copy buttons-html5 btn btn-sm btn-secondary',
                        titleAttr: 'Copy'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i>',
                        className: 'buttons-excel buttons-html5 btn btn-sm btn-success',
                        titleAttr: 'Export to Excel'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa-solid fa-file-pdf"></i>',
                        className: 'buttons-pdf buttons-html5 btn btn-sm btn-danger',
                        titleAttr: 'Export to PDF'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa-solid fa-print"></i>',
                        className: 'buttons-print btn btn-sm btn-warning',
                        titleAttr: 'Print'
                    },
                    {
                        text: '<i class="fa-solid fa-eye"></i>',
                        className: 'btn btn-sm btn-info',
                        titleAttr: 'Visible',
                        action: function(e, dt, node, config) {
                            var columns = dt.columns().indexes().toArray();
                            var visibilityToggle = '';

                            columns.forEach(function(index) {
                                var column = dt.column(index);
                                var isVisible = column.visible();
                                var columnName = column.header()
                                    .innerText; // Get the column name

                                visibilityToggle +=
                                    '<div class="switcher"><label>' +
                                    '<input type="checkbox" class="column-toggle" data-column="' +
                                    index + '" ' + (isVisible ? 'checked' : '') + '>' +
                                    '<span class="slider"></span>&nbsp;' + columnName +
                                    '</label></div>'; // Use columnName instead
                            });

                            var $dialog = $('<div></div>').html(visibilityToggle).dialog({
                                title: 'Toggle Column Visibility',
                                modal: true,
                                closeOnEscape: true,
                                open: function() {
                                    $(this).closest('.ui-dialog').find(
                                            '.ui-dialog-titlebar-close')
                                        .hide(); // Hide the default close button
                                },
                                buttons: {
                                    'Apply': {
                                        text: 'Apply',
                                        class: 'btn btn-sm btn-secondary',
                                        click: function() {
                                            $('.column-toggle').each(function() {
                                                var columnIndex = $(this).data(
                                                    'column');
                                                var column = dt.column(
                                                    columnIndex);
                                                column.visible(this.checked);
                                            });
                                            $dialog.dialog('close');
                                        }
                                    },
                                    'Cancel': {
                                        text: 'Cancel',
                                        class: 'btn btn-sm btn-warning',
                                        click: function() {
                                            $dialog.dialog('close');
                                        }
                                    },
                                }
                            });
                        }
                    },
                    {
                        text: '<i data-feather="refresh-cw"></i>',
                        className: 'btn btn-sm btn-dark',
                        titleAttr: 'Refresh',
                        action: function(e, dt, node, config) {
                            dt.ajax.reload();
                        }
                    }
                ],
                language: {
                    search: '', // Remove "Search:" text
                    searchPlaceholder: 'Search...', // Optional: add a placeholder instead
                },
            });
            feather.replace();

            $('#saveUnitsBtn').on('click', function(e) {
                e.preventDefault();
                const form = $('#createUnitsForm');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('units.store') }}', // Use the named route
                    data: form.serialize(),
                    success: function(response) {
                        $('#createUnitsModal').modal('hide');
                        $('#unitsTable').DataTable().ajax.reload(); // Reload the table data
                        Swal.fire(
                            'Success!',
                            'Good, Units has been added.',
                            'success'
                        );
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        for (const key in errors) {
                            alert(errors[key].join(', ')); // Display errors
                        }
                    }
                });
            });
        });
        // Delete Units with SweetAlert
        // Function to delete product
        function deleteUnits(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6900c7',
                cancelButtonColor: '#f4a100',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/units/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#unitsTable').DataTable().ajax.reload();
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            );
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the product.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function duplicateUnits(id) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to duplicate this units?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6900c7',
                cancelButtonColor: '#f4a100',
                confirmButtonText: 'Yes, duplicate it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with the AJAX request to duplicate the product
                    $.ajax({
                        url: '/unit/' + id + '/duplicate',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#unitsTable').DataTable().ajax.reload();
                            Swal.fire('Success!', response.success, 'success');
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'There was an error duplicating the units.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function viewUnits(id) {
            // Make an AJAX request to fetch the product details
            $.ajax({
                url: '/units/' + id, // Adjust the URL based on your route
                method: 'GET',
                success: function(data) {
                    // Populate your modal or display area with product data
                    $('#viewUnitsModal .modal-body').html(`
                <p><strong>Name:</strong> ${data.name}</p>
            `);
                    $('#viewUnitsModal').modal('show'); // Show the modal
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Erorr fetching unit.',
                        'error'
                    );
                }
            });
        }

        function editUnits(id) {
            $.ajax({
                url: '/units/' + id + '/edit',
                method: 'GET',
                success: function(data) {
                    $('#editUnitsForm').find('input[name="id"]').val(data.unit.id);
                    $('#editUnitsForm').find('input[name="name"]').val(data.unit.name);
                    $('#editUnitsModal').modal('show'); // Show the modal
                },
                error: function() {
                    alert('Error fetching unit for editing.');
                }
            });
        }
    </script>
@endpush
@section('contenthead')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="list"></i></div>
                            Units List
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#createUnitsModal">
                            <i class="fa-solid fa-plus"></i> Create Units
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection
@section('content')
    <div class="container-fluid px-4">
        <div class="card">
            <div class="card-header">
                Units
            </div>
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="DataTable Actions">
                    <!-- Buttons will be rendered here by DataTables -->
                </div>
                <table id="unitsTable" class="table table-striped table-hover table-responsive" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- View Units Modal -->
    <div id="viewUnitsModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Units</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Units details will be populated here -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Units Modal -->
    <div id="editUnitsModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Units</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUnitsForm" action="/units/update" method="POST">
                    @csrf
                    @method('PUT') <!-- Use this to specify the PUT method -->
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning" type="button" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Units Modal -->
    <div id="createUnitsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createUnitsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUnitsModalLabel">Create Units</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createUnitsForm">
                        @csrf
                        <div class="form-group">
                            <label for="name">Units Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-warning" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-secondary" id="saveUnitsBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection
