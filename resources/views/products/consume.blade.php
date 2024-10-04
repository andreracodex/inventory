@extends('layouts.pos')
@section('content')
    <div class="container-fluid">

        <h1 class="mt-4">Consumable Products</h1>

        <form method="GET" action="{{ route('products.pos') }}" class="mb-4">
            <div class="input-group input-group-joined">
                <span class="input-group-text">
                    <i data-feather="search"></i>
                </span>
                <input type="text" name="search" class="form-control ps-0" placeholder="Search products..."
                    aria-label="Search" value="{{ request('search') }}">
            </div>
        </form>

        <!-- Add your search form here -->

        @if ($products->isEmpty())
            <div class="alert alert-secondary alert-icon" role="alert">
                <button class="btn-close place-order" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                <div class="alert-icon-aside">
                    <i data-feather="feather"></i>
                </div>
                <div class="alert-icon-content">
                    <h6 class="alert-heading">Ooops, Some Thing Missing</h6>
                    Please Search Other Consumable
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-9">
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-md-3 mb-4">
                                <div class="card">
                                    <div class="row g-0">
                                        <div class="col-md-6">
                                            <img src="{{ asset($product->url_images) }}" class="img-fluid"
                                                alt="{{ $product->name }}" style="object-fit: cover; height: 100%;">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $product->name }}</h5>
                                                <p class="card-text" id="stock-{{ $product->id }}">Stock:
                                                    {{ $product->stock }}</p> <!-- Add id for stock -->
                                                <button class="btn btn-sm btn-secondary add-to-cart"
                                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                    data-price="{{ $product->price }}"
                                                    aria-label="Add {{ $product->name }} to cart"><i
                                                        data-feather="plus"></i>&nbsp;Consume</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3">
                    <!-- Order Summary -->
                    <h3 class="mt-4">Your Cart</h3>
                    <div id="cart-summary">
                        <ul id="cart-items" class="list-group mb-4"></ul>
                        <p id="total-amount"></p>
                        <button class="btn btn-sm btn-primary place-order"><i data-feather="shopping-bag"></i>&nbsp;Place
                            Order</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@push('script')
    <script>
        let cart = [];

        // Function to add items to the cart
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productPrice = parseFloat(this.getAttribute('data-price'));

                const existingItem = cart.find(item => item.product_id === productId);
                if (existingItem) {
                    existingItem.quantity += 1; // Increase quantity if already in cart
                } else {
                    cart.push({
                        product_id: productId,
                        name: productName,
                        quantity: 1,
                        price: productPrice
                    });
                }

                updateCartDisplay();
            });
        });

        // Format currency function
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        // Update cart display function
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cart-items');
            cartItemsContainer.innerHTML = ''; // Clear existing items
            let totalAmount = 0;

            cart.forEach((item, index) => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                listItem.textContent = `${item.name} - ${formatCurrency(item.price)} x ${item.quantity}`;

                // Create remove button
                const removeButton = document.createElement('button');
                removeButton.className = 'btn btn-danger btn-sm';
                removeButton.textContent = 'Remove';
                removeButton.addEventListener('click', () => {
                    cart.splice(index, 1); // Remove item from cart
                    updateCartDisplay(); // Update display
                });

                // Append remove button to list item
                listItem.appendChild(removeButton);
                cartItemsContainer.appendChild(listItem);
                totalAmount += item.price * item.quantity;
            });

            document.getElementById('total-amount').textContent = `Total: ${formatCurrency(totalAmount)}`;
        }

        // Place order event listener
        document.querySelector('.place-order').addEventListener('click', function() {
            if (cart.length === 0) {
                Swal.fire(
                    'Warning!',
                    'Oopps, Product Cannot Be Empty.',
                    'warning'
                );
                return;
            }

            const totalAmount = cart.reduce((total, item) => total + (item.price * item.quantity), 0);

            fetch('/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        items: cart,
                        total_amount: totalAmount
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    Swal.fire(
                        'Success!',
                        'Good, consumable product has been added.',
                        'success'
                    );
                    cart = []; // Clear the cart after successful order
                    updateCartDisplay(); // Update cart display
                })
                .catch(error => {
                    console.error('Error placing order:', error);
                });
        });
    </script>
@endpush
