@extends('layouts.app')
@push('script')
    <script>
        $(document).ready(function() {
            var table = $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('products.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'price',
                        name: 'price',
                        render: function(data) {
                            // Format the price to Rupiah
                            return 'Rp ' + Number(data).toLocaleString('id-ID');
                        },
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'unit_name',
                        name: 'unit_name'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<div class="btn-group" role="group" aria-label="Product Actions">' +
                                '<button class="btn btn-sm btn-view btn-info" title="View Data" data-id="' +
                                row.id +
                                '" onclick="viewProduct(' + row.id +
                                ')"><i class="fa-solid fa-eye"></i></button>' +
                                '<button class="btn btn-sm btn-duplicate btn-secondary" title="Duplicate Data" data-id="' +
                                row.id + '" onclick="duplicateProduct(' + row.id +
                                ')"><i class="fa-solid fa-copy"></i></button>' +
                                '<button class="btn btn-sm btn-edit btn-warning" title="Edit Data" data-id="' +
                                row
                                .id + '" onclick="editProduct(' + row.id +
                                ')"><i class="fa-solid fa-pencil"></i></button>' +
                                '<button class="btn btn-sm btn-delete btn-danger" title="Delete Data" onclick="deleteProduct(' +
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

            $('#saveProductBtn').on('click', function(e) {
                e.preventDefault();
                const form = $('#createProductForm');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('products.store') }}', // Use the named route
                    data: form.serialize(),
                    success: function(response) {
                        $('#createProductModal').modal('hide');
                        $('#productsTable').DataTable().ajax.reload(); // Reload the table data
                        Swal.fire(
                            'Success!',
                            'Good, Product has been added.',
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
        // Delete Product with SweetAlert
        // Function to delete product
        function deleteProduct(id) {
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
                        url: '/products/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#productsTable').DataTable().ajax.reload();
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

        function duplicateProduct(id) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to duplicate this product?",
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
                        url: '/products/' + id + '/duplicate',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#productsTable').DataTable().ajax.reload();
                            Swal.fire('Success!', response.success, 'success');
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'There was an error duplicating the product.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function viewProduct(id) {
            // Make an AJAX request to fetch the product details
            $.ajax({
                url: '/products/' + id, // Adjust the URL based on your route
                method: 'GET',
                success: function(data) {
                    // Populate your modal or display area with product data
                    $('#viewProductModal .modal-body').html(`
                <p><strong>Name:</strong> ${data.name}</p>
                <p><strong>Price:</strong> Rp ${Number(data.price).toLocaleString('id-ID')}</p>
                <p><strong>Category:</strong> ${data.category.name}</p>
                <p><strong>Unit:</strong> ${data.unit.name}</p>
                <p><strong>Minimal Stock:</strong> ${data.minimal_stock}</p>
                <p><strong>Stock:</strong> ${data.stock}</p>
            `);
                    $('#viewProductModal').modal('show'); // Show the modal
                },
                error: function() {
                    alert('Error fetching product details.');
                }
            });
        }

        function editProduct(id) {
            $.ajax({
                url: '/products/' + id + '/edit',
                method: 'GET',
                success: function(data) {
                    // Populate form fields
                    let originalPrice = data.product.price;
                    // Round down the price using Math.floor()
                    let roundedPrice = Math.floor(originalPrice);
                    $('#editProductForm').find('input[name="id"]').val(data.product.id);
                    $('#editProductForm').find('input[name="name"]').val(data.product.name);
                    $('#editProductForm').find('input[name="price"]').val(roundedPrice);
                    $('#editProductForm').find('input[name="minimal_stock"]').val(data.product.minimal_stock);
                    $('#editProductForm').find('input[name="stock"]').val(data.product.stock);

                    // Set the selected category
                    $('#editProductForm').find('select[name="category_id"]').val(data.product.category_id);

                    // Set the selected unit
                    $('#editProductForm').find('select[name="unit_id"]').val(data.product.unit_id);

                    // Populate categories
                    const categoriesSelect = $('#editProductForm').find('select[name="category_id"]');
                    const unitsSelect = $('#editProductForm').find('select[name="unit_id"]');

                    // Clear existing options
                    categoriesSelect.empty();
                    unitsSelect.empty();

                    // Populate categories
                    $.each(data.categories, function(index, category) {
                        categoriesSelect.append(new Option(category.name, category.id));
                    });

                    // Populate units
                    $.each(data.units, function(index, unit) {
                        unitsSelect.append(new Option(unit.name, unit.id));
                    });

                    // Set selected category and unit after populating
                    categoriesSelect.val(data.product.category_id);
                    unitsSelect.val(data.product.unit_id);

                    $('#editProductModal').modal('show'); // Show the modal
                },
                error: function() {
                    alert('Error fetching product details for editing.');
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
                            Product List
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#createProductModal">
                            <i class="fa-solid fa-plus"></i> Create Product
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
                Products
            </div>
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="DataTable Actions">
                    <!-- Buttons will be rendered here by DataTables -->
                </div>
                <table id="productsTable" class="table table-striped table-hover table-responsive" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Unit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- View Product Modal -->
    <div id="viewProductModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Product</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Product details will be populated here -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editProductForm" action="/products/update" method="POST">
                    @csrf
                    @method('PUT') <!-- Use this to specify the PUT method -->
                    <input type="hidden" name="id">
                    <input type="text" hidden aria-hidden="true" name="user_id" id="user_id"
                        value="{{ Auth::user()->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" min="0" class="form-control" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" class="form-control" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="unit_id">Unit</label>
                            <select name="unit_id" class="form-control" required>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="minimal_stock">Minimal Stock</label>
                            <input type="number" min="0" class="form-control" name="minimal_stock" required>
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" min="0" class="form-control" name="stock" required>
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

    <!-- Create Product Modal -->
    <div id="createProductModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="createProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProductModalLabel">Create Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createProductForm">
                        @csrf
                        <input type="text" hidden aria-hidden="true" name="user_id" id="user_id"
                            value="{{ Auth::user()->id }}">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" min="0" class="form-control" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" class="form-control" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="unit_id">Unit</label>
                            <select name="unit_id" class="form-control" required>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="minimal_stock">Minimal Stock</label>
                            <input type="number" min="0" class="form-control" name="minimal_stock" required>
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" min="0" class="form-control" name="stock" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-warning" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-secondary" id="saveProductBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection
